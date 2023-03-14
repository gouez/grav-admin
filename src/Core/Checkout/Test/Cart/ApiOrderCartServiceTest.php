<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Cart;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\Cart\ApiOrderCartService;
use Laser\Core\Checkout\Cart\CartPersister;
use Laser\Core\Checkout\Promotion\Cart\PromotionCollector;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextFactory;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextPersister;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextService;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Laser\Core\Test\TestDefaults;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @internal
 */
#[Package('checkout')]
class ApiOrderCartServiceTest extends TestCase
{
    use IntegrationTestBehaviour;

    private Connection $connection;

    private SalesChannelContextPersister $contextPersister;

    private SalesChannelContext $salesChannelContext;

    private ApiOrderCartService $adminOrderCartService;

    protected function setUp(): void
    {
        $this->connection = $this->getContainer()->get(Connection::class);
        $eventDispatcher = new EventDispatcher();
        $this->contextPersister = new SalesChannelContextPersister($this->connection, $eventDispatcher, $this->getContainer()->get(CartPersister::class));
        $this->salesChannelContext = $this->getContainer()->get(SalesChannelContextFactory::class)
            ->create(Uuid::randomHex(), TestDefaults::SALES_CHANNEL);
        $this->adminOrderCartService = $this->getContainer()->get(ApiOrderCartService::class);
    }

    public function testAddPermission(): void
    {
        $this->adminOrderCartService->addPermission($this->salesChannelContext->getToken(), PromotionCollector::SKIP_AUTOMATIC_PROMOTIONS, $this->salesChannelContext->getSalesChannelId());
        $payload = $this->contextPersister->load($this->salesChannelContext->getToken(), $this->salesChannelContext->getSalesChannelId());
        static::assertArrayHasKey(PromotionCollector::SKIP_AUTOMATIC_PROMOTIONS, $payload[SalesChannelContextService::PERMISSIONS]);
        static::assertTrue($payload[SalesChannelContextService::PERMISSIONS][PromotionCollector::SKIP_AUTOMATIC_PROMOTIONS]);
    }

    public function testAddMultiplePermissions(): void
    {
        $this->adminOrderCartService->addPermission($this->salesChannelContext->getToken(), PromotionCollector::SKIP_AUTOMATIC_PROMOTIONS, $this->salesChannelContext->getSalesChannelId());
        $this->adminOrderCartService->addPermission($this->salesChannelContext->getToken(), PromotionCollector::SKIP_PROMOTION, $this->salesChannelContext->getSalesChannelId());
        $payload = $this->contextPersister->load($this->salesChannelContext->getToken(), $this->salesChannelContext->getSalesChannelId());

        static::assertArrayHasKey(SalesChannelContextService::PERMISSIONS, $payload);
        static::assertCount(2, $payload[SalesChannelContextService::PERMISSIONS]);
        static::assertArrayHasKey(PromotionCollector::SKIP_AUTOMATIC_PROMOTIONS, $payload[SalesChannelContextService::PERMISSIONS]);
        static::assertTrue($payload[SalesChannelContextService::PERMISSIONS][PromotionCollector::SKIP_AUTOMATIC_PROMOTIONS]);

        static::assertArrayHasKey(PromotionCollector::SKIP_PROMOTION, $payload[SalesChannelContextService::PERMISSIONS]);
        static::assertTrue($payload[SalesChannelContextService::PERMISSIONS][PromotionCollector::SKIP_PROMOTION]);
    }

    public function testDeletePermission(): void
    {
        $this->adminOrderCartService->addPermission($this->salesChannelContext->getToken(), PromotionCollector::SKIP_AUTOMATIC_PROMOTIONS, $this->salesChannelContext->getSalesChannelId());
        $payload = $this->contextPersister->load($this->salesChannelContext->getToken(), $this->salesChannelContext->getSalesChannelId());
        static::assertArrayHasKey(PromotionCollector::SKIP_AUTOMATIC_PROMOTIONS, $payload[SalesChannelContextService::PERMISSIONS]);
        static::assertTrue($payload[SalesChannelContextService::PERMISSIONS][PromotionCollector::SKIP_AUTOMATIC_PROMOTIONS]);

        $this->adminOrderCartService->deletePermission($this->salesChannelContext->getToken(), PromotionCollector::SKIP_AUTOMATIC_PROMOTIONS, $this->salesChannelContext->getSalesChannelId());
        $payload = $this->contextPersister->load($this->salesChannelContext->getToken(), $this->salesChannelContext->getSalesChannelId());
        static::assertArrayHasKey(PromotionCollector::SKIP_AUTOMATIC_PROMOTIONS, $payload[SalesChannelContextService::PERMISSIONS]);
        static::assertFalse($payload[SalesChannelContextService::PERMISSIONS][PromotionCollector::SKIP_AUTOMATIC_PROMOTIONS]);
    }
}
