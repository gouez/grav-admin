<?php declare(strict_types=1);

namespace Laser\Core\Content\ProductExport\Service;

use Monolog\Level;
use Laser\Core\Content\ProductExport\Event\ProductExportLoggingEvent;
use Laser\Core\Content\ProductExport\Exception\ExportInvalidException;
use Laser\Core\Content\ProductExport\Exception\ExportNotFoundException;
use Laser\Core\Content\ProductExport\Exception\ExportNotGeneratedException;
use Laser\Core\Content\ProductExport\ProductExportCollection;
use Laser\Core\Content\ProductExport\ProductExportEntity;
use Laser\Core\Content\ProductExport\Struct\ExportBehavior;
use Laser\Core\Content\ProductExport\Struct\ProductExportResult;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[Package('sales-channel')]
class ProductExporter implements ProductExporterInterface
{
    /**
     * @internal
     */
    public function __construct(
        private readonly EntityRepository $productExportRepository,
        private readonly ProductExportGeneratorInterface $productExportGenerator,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly ProductExportFileHandlerInterface $productExportFileHandler
    ) {
    }

    public function export(
        SalesChannelContext $context,
        ExportBehavior $behavior,
        ?string $productExportId = null
    ): void {
        $criteria = new Criteria();
        if ($productExportId !== null) {
            $criteria = new Criteria(array_filter([$productExportId]));
        }

        $criteria
            ->addAssociation('salesChannel')
            ->addAssociation('salesChannelDomain.salesChannel')
            ->addAssociation('salesChannelDomain.language.locale')
            ->addAssociation('productStream.filters.queries')
            ->addFilter(
                new MultiFilter(
                    'OR',
                    [
                        new EqualsFilter('salesChannelId', $context->getSalesChannel()->getId()),
                        new EqualsFilter('salesChannelDomain.salesChannel.id', $context->getSalesChannel()->getId()),
                    ]
                )
            );

        if (!$behavior->includeInactive()) {
            $criteria->addFilter(new EqualsFilter('salesChannel.active', true));
        }

        /** @var ProductExportCollection $productExports */
        $productExports = $this->productExportRepository->search($criteria, $context->getContext());

        if ($productExports->count() === 0) {
            $exportNotFoundException = new ExportNotFoundException($productExportId);
            $this->logException($context->getContext(), $exportNotFoundException);

            throw $exportNotFoundException;
        }

        foreach ($productExports as $productExport) {
            $this->createFile($productExport, $context, $behavior);
        }
    }

    private function createFile(
        ProductExportEntity $productExport,
        SalesChannelContext $context,
        ExportBehavior $exportBehavior
    ): void {
        $filePath = $this->productExportFileHandler->getFilePath($productExport);

        if ($this->productExportFileHandler->isValidFile(
            $filePath,
            $exportBehavior,
            $productExport
        )) {
            return;
        }
        $result = $this->productExportGenerator->generate($productExport, $exportBehavior);
        if (!$result instanceof ProductExportResult) {
            $exportNotGeneratedException = new ExportNotGeneratedException();
            $this->logException($context->getContext(), $exportNotGeneratedException);

            throw $exportNotGeneratedException;
        }

        if ($result->hasErrors()) {
            $exportInvalidException = new ExportInvalidException($productExport, $result->getErrors());
            $this->logException($context->getContext(), $exportInvalidException);

            throw $exportInvalidException;
        }

        $writeProductExportSuccessful = $this->productExportFileHandler->writeProductExportContent(
            $result->getContent(),
            $filePath
        );

        if (!$writeProductExportSuccessful) {
            return;
        }

        $this->productExportRepository->update(
            [
                [
                    'id' => $productExport->getId(),
                    'generatedAt' => new \DateTime(),
                ],
            ],
            $context->getContext()
        );
    }

    private function logException(Context $context, \Exception $exception): void
    {
        $loggingEvent = new ProductExportLoggingEvent(
            $context,
            $exception->getMessage(),
            Level::Warning,
            $exception
        );

        $this->eventDispatcher->dispatch($loggingEvent);
    }
}
