<?php declare(strict_types=1);

namespace Laser\Core\Content\Sitemap\ScheduledTask;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

#[Package('sales-channel')]
class SitemapGenerateTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'laser.sitemap_generate';
    }

    public static function getDefaultInterval(): int
    {
        return 86400;
    }
}
