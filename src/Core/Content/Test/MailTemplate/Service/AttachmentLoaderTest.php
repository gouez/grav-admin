<?php declare(strict_types=1);

namespace Laser\Core\Content\Test\MailTemplate\Service;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\Document\DocumentEntity;
use Laser\Core\Checkout\Document\Renderer\InvoiceRenderer;
use Laser\Core\Checkout\Document\Service\DocumentGenerator;
use Laser\Core\Checkout\Document\Service\PdfRenderer;
use Laser\Core\Checkout\Document\Struct\DocumentGenerateOperation;
use Laser\Core\Checkout\Test\Document\DocumentTrait;
use Laser\Core\Content\MailTemplate\Service\AttachmentLoader;
use Laser\Core\Content\MailTemplate\Service\Event\AttachmentLoaderCriteriaEvent;
use Laser\Core\Content\Media\MediaService;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextFactory;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextService;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Laser\Core\Test\TestDefaults;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 */
class AttachmentLoaderTest extends TestCase
{
    use DocumentTrait;

    private AttachmentLoader $attachmentLoader;

    private DocumentGenerator $documentGenerator;

    /**
     * @var EventDispatcherInterface&MockObject
     */
    private EventDispatcherInterface $eventDispatcherMock;

    private SalesChannelContext $salesChannelContext;

    private Context $context;

    protected function setUp(): void
    {
        $this->documentGenerator = $this->getContainer()->get(DocumentGenerator::class);
        $this->eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);

        $this->attachmentLoader = new AttachmentLoader(
            $this->getContainer()->get('document.repository'),
            $this->documentGenerator,
            $this->eventDispatcherMock
        );

        $this->context = Context::createDefaultContext();

        $customerId = $this->createCustomer();

        $this->salesChannelContext = $this->getContainer()->get(SalesChannelContextFactory::class)->create(
            Uuid::randomHex(),
            TestDefaults::SALES_CHANNEL,
            [
                SalesChannelContextService::CUSTOMER_ID => $customerId,
            ]
        );
    }

    public function testLoad(): void
    {
        $this->eventDispatcherMock->expects(static::once())->method('dispatch')->with(static::callback(static function (AttachmentLoaderCriteriaEvent $event) {
            $criteria = $event->getCriteria();

            return $criteria->hasAssociation('documentMediaFile') && $criteria->hasAssociation('documentType');
        }));

        $cart = $this->generateDemoCart(2);
        $orderId = $this->persistCart($cart);

        $operation = new DocumentGenerateOperation($orderId);

        $document = $this->documentGenerator->generate(InvoiceRenderer::TYPE, [$orderId => $operation], $this->context)->getSuccess()->first();

        static::assertNotNull($document);

        $attachments = $this->attachmentLoader->load([$document->getId()], Context::createDefaultContext());
        static::assertCount(1, $attachments);
        static::assertIsArray($attachments[0]);
        static::assertArrayHasKey('content', $attachments[0]);

        $criteria = new Criteria([$document->getId()]);
        $criteria->addAssociation('documentMediaFile');

        /** @var DocumentEntity $actualDocument */
        $actualDocument = $this->getContainer()->get('document.repository')->search($criteria, $this->context)->first();

        static::assertNotNull($actualDocument);
        static::assertNotNull($actualDocument->getDocumentMediaFileId());
        static::assertNotNull($actualDocument->getDocumentMediaFile());

        $content = $this->getContainer()->get(MediaService::class)->loadFile($actualDocument->getDocumentMediaFileId(), $this->context);

        $fileName = $actualDocument->getDocumentMediaFile()->getFileName() . '.' . $actualDocument->getDocumentMediaFile()->getFileExtension();
        static::assertNotNull($content);
        static::assertSame($content, $attachments[0]['content']);
        static::assertArrayHasKey('fileName', $attachments[0]);
        static::assertSame($fileName, $attachments[0]['fileName']);
        static::assertArrayHasKey('mimeType', $attachments[0]);
        static::assertSame(PdfRenderer::FILE_CONTENT_TYPE, $attachments[0]['mimeType']);
    }
}
