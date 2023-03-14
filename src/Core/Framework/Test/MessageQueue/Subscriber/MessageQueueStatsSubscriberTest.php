<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\MessageQueue\Subscriber;

use PHPUnit\Framework\TestCase;
use Laser\Core\Framework\Increment\AbstractIncrementer;
use Laser\Core\Framework\Increment\IncrementGatewayRegistry;
use Laser\Core\Framework\Test\MessageQueue\fixtures\BarMessage;
use Laser\Core\Framework\Test\MessageQueue\fixtures\FooMessage;
use Laser\Core\Framework\Test\MessageQueue\fixtures\NoHandlerMessage;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Laser\Core\Framework\Test\TestCaseBase\QueueTestBehaviour;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @internal
 */
class MessageQueueStatsSubscriberTest extends TestCase
{
    use QueueTestBehaviour;
    use IntegrationTestBehaviour;

    public function testListener(): void
    {
        /** @var AbstractIncrementer $pool */
        $pool = $this->getContainer()
            ->get('laser.increment.gateway.registry')
            ->get(IncrementGatewayRegistry::MESSAGE_QUEUE_POOL);

        $pool->reset('message_queue_stats');

        /** @var MessageBusInterface $bus */
        $bus = $this->getContainer()->get('messenger.bus.test_laser');

        $bus->dispatch(new FooMessage());
        $bus->dispatch(new BarMessage());
        $bus->dispatch(new BarMessage());
        $bus->dispatch(new BarMessage());

        $stats = $pool->list('message_queue_stats');
        static::assertEquals(1, $stats[FooMessage::class]['count']);
        static::assertEquals(3, $stats[BarMessage::class]['count']);

        $this->runWorker();

        $stats = $pool->list('message_queue_stats');
        static::assertEquals(0, $stats[FooMessage::class]['count']);
        static::assertEquals(0, $stats[BarMessage::class]['count']);

        $bus->dispatch(new NoHandlerMessage());

        $stats = $pool->list('message_queue_stats');
        static::assertEquals(1, $stats[NoHandlerMessage::class]['count']);

        $this->runWorker();
        $stats = $pool->list('message_queue_stats');
        static::assertEquals(0, $stats[NoHandlerMessage::class]['count']);
    }
}
