<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Document\Renderer;

use Doctrine\DBAL\Connection;
use Laser\Core\Checkout\Document\Event\DeliveryNoteOrdersEvent;
use Laser\Core\Checkout\Document\Service\DocumentConfigLoader;
use Laser\Core\Checkout\Document\Struct\DocumentGenerateOperation;
use Laser\Core\Checkout\Document\Twig\DocumentTemplateRenderer;
use Laser\Core\Checkout\Order\OrderCollection;
use Laser\Core\Checkout\Order\OrderEntity;
use Laser\Core\Defaults;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Plugin\Exception\DecorationPatternException;
use Laser\Core\System\Locale\LocaleEntity;
use Laser\Core\System\NumberRange\ValueGenerator\NumberRangeValueGeneratorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Package('customer-order')]
final class DeliveryNoteRenderer extends AbstractDocumentRenderer
{
    public const TYPE = 'delivery_note';

    /**
     * @internal
     */
    public function __construct(
        private readonly EntityRepository $orderRepository,
        private readonly DocumentConfigLoader $documentConfigLoader,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly DocumentTemplateRenderer $documentTemplateRenderer,
        private readonly NumberRangeValueGeneratorInterface $numberRangeValueGenerator,
        private readonly string $rootDir,
        private readonly Connection $connection
    ) {
    }

    public function supports(): string
    {
        return self::TYPE;
    }

    public function render(array $operations, Context $context, DocumentRendererConfig $rendererConfig): RendererResult
    {
        $result = new RendererResult();

        $template = '@Framework/documents/delivery_note.html.twig';

        $ids = \array_map(fn (DocumentGenerateOperation $operation) => $operation->getOrderId(), $operations);

        if (empty($ids)) {
            return $result;
        }

        $chunk = $this->getOrdersLanguageId(array_values($ids), $context->getVersionId(), $this->connection);

        foreach ($chunk as ['language_id' => $languageId, 'ids' => $ids]) {
            $criteria = OrderDocumentCriteriaFactory::create(explode(',', (string) $ids), $rendererConfig->deepLinkCode);
            $context = $context->assign([
                'languageIdChain' => array_unique(array_filter([$languageId, $context->getLanguageId()])),
            ]);

            // TODO: future implementation (only fetch required data and associations)

            /** @var OrderCollection $orders */
            $orders = $this->orderRepository->search($criteria, $context)->getEntities();

            $this->eventDispatcher->dispatch(new DeliveryNoteOrdersEvent($orders, $context, $operations));

            foreach ($orders as $order) {
                $orderId = $order->getId();

                try {
                    if (!\array_key_exists($order->getId(), $operations)) {
                        continue;
                    }

                    /** @var DocumentGenerateOperation $operation */
                    $operation = $operations[$order->getId()];

                    $config = clone $this->documentConfigLoader->load(self::TYPE, $order->getSalesChannelId(), $context);

                    $config->merge($operation->getConfig());

                    $number = $config->getDocumentNumber() ?: $this->getNumber($context, $order, $operation);

                    $now = (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT);
                    $customConfig = $operation->getConfig()['custom'] ?? [];

                    $config->merge([
                        'documentNumber' => $number,
                        'documentDate' => $operation->getConfig()['documentDate'] ?? $now,
                        'custom' => [
                            'deliveryNoteNumber' => $number,
                            'deliveryDate' => $customConfig['deliveryDate'] ?? $now,
                            'deliveryNoteDate' => $customConfig['deliveryNoteDate'] ?? $now,
                        ],
                    ]);

                    if ($operation->isStatic()) {
                        $doc = new RenderedDocument('', $number, $config->buildName(), $operation->getFileType(), $config->jsonSerialize());
                        $result->addSuccess($orderId, $doc);

                        continue;
                    }

                    $deliveries = null;
                    if ($order->getDeliveries()) {
                        $deliveries = $order->getDeliveries()->first();
                    }

                    /** @var LocaleEntity $locale */
                    $locale = $order->getLanguage()->getLocale();

                    $html = $this->documentTemplateRenderer->render(
                        $template,
                        [
                            'order' => $order,
                            'orderDelivery' => $deliveries,
                            'config' => $config,
                            'rootDir' => $this->rootDir,
                            'context' => $context,
                        ],
                        $context,
                        $order->getSalesChannelId(),
                        $order->getLanguageId(),
                        $locale->getCode()
                    );

                    $doc = new RenderedDocument(
                        $html,
                        $number,
                        $config->buildName(),
                        $operation->getFileType(),
                        $config->jsonSerialize(),
                    );

                    $result->addSuccess($orderId, $doc);
                } catch (\Throwable $exception) {
                    $result->addError($orderId, $exception);
                }
            }
        }

        return $result;
    }

    public function getDecorated(): AbstractDocumentRenderer
    {
        throw new DecorationPatternException(self::class);
    }

    private function getNumber(Context $context, OrderEntity $order, DocumentGenerateOperation $operation): string
    {
        return $this->numberRangeValueGenerator->getValue(
            'document_' . self::TYPE,
            $context,
            $order->getSalesChannelId(),
            $operation->isPreview()
        );
    }
}
