<?php declare(strict_types=1);

namespace Laser\Core\Framework\Webhook\ScheduledTask;

use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Laser\Core\Framework\Webhook\Service\WebhookCleanup;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @internal
 */
#[AsMessageHandler(handles: CleanupWebhookEventLogTask::class)]
#[Package('core')]
final class CleanupWebhookEventLogTaskHandler extends ScheduledTaskHandler
{
    /**
     * @internal
     */
    public function __construct(
        EntityRepository $repository,
        private readonly WebhookCleanup $webhookCleanup
    ) {
        parent::__construct($repository);
    }

    public function run(): void
    {
        $this->webhookCleanup->removeOldLogs();
    }
}
