<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\MessageQueue\fixtures;

use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskDefinition;
use Laser\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskEntity;
use Laser\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @internal
 */
#[AsMessageHandler(handles: TestTask::class)]
final class DummyScheduledTaskHandler extends ScheduledTaskHandler
{
    private bool $wasCalled = false;

    public function __construct(
        EntityRepository $scheduledTaskRepository,
        private readonly string $taskId,
        private readonly bool $shouldThrowException = false
    ) {
        parent::__construct($scheduledTaskRepository);
    }

    public function run(): void
    {
        $this->wasCalled = true;

        /** @var ScheduledTaskEntity $task */
        $task = $this->scheduledTaskRepository
            ->search(new Criteria([$this->taskId]), Context::createDefaultContext())
            ->get($this->taskId);

        if ($task->getStatus() !== ScheduledTaskDefinition::STATUS_RUNNING) {
            throw new \Exception('Scheduled Task was not marked as running.');
        }

        if ($this->shouldThrowException) {
            throw new \RuntimeException('This Exception should be thrown');
        }
    }

    public function wasCalled(): bool
    {
        return $this->wasCalled;
    }
}
