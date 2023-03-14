<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Document\Service;

use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\Document\DocumentConfiguration;
use Laser\Core\Checkout\Document\DocumentConfigurationFactory;
use Laser\Core\Checkout\Document\Renderer\InvoiceRenderer;
use Laser\Core\Checkout\Document\Service\DocumentConfigLoader;
use Laser\Core\Checkout\Test\Document\DocumentTrait;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextFactory;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextService;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Laser\Core\Test\TestDefaults;

/**
 * @internal
 */
#[Package('customer-order')]
class DocumentConfigLoaderTest extends TestCase
{
    use DocumentTrait;

    private SalesChannelContext $salesChannelContext;

    private Context $context;

    private DocumentConfigLoader $documentConfigLoader;

    protected function setUp(): void
    {
        parent::setUp();

        $this->context = Context::createDefaultContext();

        $customerId = $this->createCustomer();

        $this->salesChannelContext = $this->getContainer()->get(SalesChannelContextFactory::class)->create(
            Uuid::randomHex(),
            TestDefaults::SALES_CHANNEL,
            [
                SalesChannelContextService::CUSTOMER_ID => $customerId,
            ]
        );

        $this->documentConfigLoader = $this->getContainer()->get(DocumentConfigLoader::class);
    }

    protected function tearDown(): void
    {
        $this->documentConfigLoader->reset();
    }

    public function testLoadGlobalConfig(): void
    {
        $base = $this->getBaseConfig('invoice');
        $globalConfig = $base === null ? [] : $base->getConfig();
        $globalConfig['companyName'] = 'Test corp.';
        $globalConfig['displayCompanyAddress'] = true;
        $this->upsertBaseConfig($globalConfig, 'invoice');

        $salesChannelId = $this->salesChannelContext->getSalesChannel()->getId();
        $config = $this->documentConfigLoader->load('invoice', $salesChannelId, $this->context);

        static::assertInstanceOf(DocumentConfiguration::class, $config);

        $config = $config->jsonSerialize();

        static::assertEquals('Test corp.', $config['companyName']);
        static::assertTrue($config['displayCompanyAddress']);
    }

    public function testLoadSalesChannelConfig(): void
    {
        $base = $this->getBaseConfig('invoice');

        $globalConfig = DocumentConfigurationFactory::createConfiguration([
            'companyName' => 'Test corp.',
            'displayCompanyAddress' => true,
        ], $base);

        $this->upsertBaseConfig($globalConfig->jsonSerialize(), InvoiceRenderer::TYPE);

        $salesChannelConfig = DocumentConfigurationFactory::mergeConfiguration($globalConfig, [
            'companyName' => 'Custom corp.',
            'displayCompanyAddress' => false,
            'pageSize' => 'a5',
        ]);

        $salesChannelId = $this->salesChannelContext->getSalesChannel()->getId();
        $this->upsertBaseConfig($salesChannelConfig->jsonSerialize(), InvoiceRenderer::TYPE, $salesChannelId);

        $config = $this->documentConfigLoader->load(InvoiceRenderer::TYPE, $salesChannelId, $this->context);

        static::assertInstanceOf(DocumentConfiguration::class, $config);

        $config = $config->jsonSerialize();

        static::assertEquals('Custom corp.', $config['companyName']);
        static::assertFalse($config['displayCompanyAddress']);
        static::assertEquals('a5', $config['pageSize']);
    }
}
