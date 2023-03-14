<?php declare(strict_types=1);

namespace Laser\Core\Framework\Log\ScheduledTask;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

#[Package('core')]
class LogCleanupTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'log_entry.cleanup';
    }

    public static function getDefaultInterval(): int
    {
        return 86400; // 24 hours
    }
}
