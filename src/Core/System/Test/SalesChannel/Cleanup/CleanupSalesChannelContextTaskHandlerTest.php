<?php declare(strict_types=1);

namespace Laser\Core\System\Test\SalesChannel\Cleanup;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Laser\Core\Defaults;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Test\IdsCollection;
use Laser\Core\Framework\Test\TestCaseBase\DatabaseTransactionBehaviour;
use Laser\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\System\SalesChannel\Context\Cleanup\CleanupSalesChannelContextTaskHandler;
use Laser\Core\Test\TestDefaults;

/**
 * @internal
 */
#[Package('sales-channel')]
class CleanupSalesChannelContextTaskHandlerTest extends TestCase
{
    use KernelTestBehaviour;
    use DatabaseTransactionBehaviour;

    private CleanupSalesChannelContextTaskHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->handler = $this->getContainer()->get(CleanupSalesChannelContextTaskHandler::class);
    }

    public function testCleanup(): void
    {
        $this->getContainer()->get(Connection::class)->executeStatement('DELETE FROM sales_channel_api_context');

        $ids = new IdsCollection();

        $this->createSalesChannelContext($ids->create('context-1'));

        $date = new \DateTime();
        $date->modify(sprintf('-%s day', 121));
        $this->createSalesChannelContext($ids->create('context-2'), $date);

        $this->handler->run();

        $contexts = $this->getContainer()->get(Connection::class)
            ->fetchFirstColumn('SELECT token FROM sales_channel_api_context');

        static::assertCount(1, $contexts);
        static::assertContains($ids->get('context-1'), $contexts);
    }

    private function createSalesChannelContext(string $token, ?\DateTime $date = null): void
    {
        $payload = [
            'token' => $token,
            'payload' => json_encode([
                'key' => 'value',
                'expired' => false,
            ]),
            'sales_channel_id' => Uuid::fromHexToBytes(TestDefaults::SALES_CHANNEL),
        ];

        if ($date) {
            $payload['updated_at'] = $date->format(Defaults::STORAGE_DATE_TIME_FORMAT);
        }

        $this->getContainer()->get(Connection::class)->insert('sales_channel_api_context', $payload);
    }
}
