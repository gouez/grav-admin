<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Customer;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

#[Package('customer-order')]
class DeleteUnusedGuestCustomerTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'customer.delete_unused_guests';
    }

    public static function getDefaultInterval(): int
    {
        return 86400; // 24 hours
    }
}
