<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\MessageQueue\Api;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Laser\Core\Content\Product\DataAbstractionLayer\ProductIndexingMessage;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Increment\AbstractIncrementer;
use Laser\Core\Framework\Increment\IncrementGatewayRegistry;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskDefinition;
use Laser\Core\Framework\Test\MessageQueue\fixtures\TestTask;
use Laser\Core\Framework\Test\TestCaseBase\AdminFunctionalTestBehaviour;
use Laser\Core\Framework\Test\TestCaseBase\QueueTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;

/**
 * @internal
 */
#[Package('system-settings')]
class ConsumeMessagesControllerTest extends TestCase
{
    use AdminFunctionalTestBehaviour;
    use QueueTestBehaviour;

    /**
     * @var AbstractIncrementer
     */
    private $gateway;

    protected function setUp(): void
    {
        $this->gateway = $this->getContainer()->get('laser.increment.gateway.registry')->get(IncrementGatewayRegistry::MESSAGE_QUEUE_POOL);
    }

    public function testConsumeMessages(): void
    {
        $connection = $this->getContainer()->get(Connection::class);
        $connection->executeStatement('DELETE FROM scheduled_task');

        // queue a task
        $repo = $this->getContainer()->get('scheduled_task.repository');
        $taskId = Uuid::randomHex();
        $repo->create([
            [
                'id' => $taskId,
                'name' => 'test',
                'scheduledTaskClass' => TestTask::class,
                'runInterval' => 300,
                'status' => ScheduledTaskDefinition::STATUS_SCHEDULED,
                'nextExecutionTime' => (new \DateTime())->modify('-1 second'),
            ],
        ], Context::createDefaultContext());

        $url = '/api/_action/scheduled-task/run';
        $client = $this->getBrowser();
        $client->request('POST', $url);

        // consume the queued task
        $url = '/api/_action/message-queue/consume';
        $client = $this->getBrowser();
        $client->request('POST', $url, ['receiver' => 'async']);

        $response = json_decode((string) $client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);
        static::assertSame(200, $client->getResponse()->getStatusCode(), \print_r($response, true));
        static::assertArrayHasKey('handledMessages', $response);
        static::assertIsInt($response['handledMessages']);
        static::assertEquals(1, $response['handledMessages']);
    }

    public function testMessageStatsDecrement(): void
    {
        $messageBus = $this->getContainer()->get('messenger.bus.laser');
        $message = new ProductIndexingMessage([Uuid::randomHex()]);
        $messageBus->dispatch($message);

        $gateway = $this->getContainer()->get('laser.increment.gateway.registry');
        $entries = $gateway->get(IncrementGatewayRegistry::MESSAGE_QUEUE_POOL)->list('message_queue_stats');

        static::assertArrayHasKey(ProductIndexingMessage::class, $entries);
        static::assertGreaterThan(0, $entries[ProductIndexingMessage::class]['count']);

        $url = '/api/_action/message-queue/consume';
        $client = $this->getBrowser();
        $client->request('POST', $url, ['receiver' => 'async']);

        $entries = $this->gateway->list('message_queue_stats');

        static::assertArrayHasKey(ProductIndexingMessage::class, $entries);
        static::assertEquals(0, $entries[ProductIndexingMessage::class]['count']);
    }
}
