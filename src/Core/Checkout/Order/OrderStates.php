<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Order;

use Laser\Core\Framework\Log\Package;

#[Package('customer-order')]
final class OrderStates
{
    public const STATE_MACHINE = 'order.state';
    public const STATE_OPEN = 'open';
    public const STATE_IN_PROGRESS = 'in_progress';
    public const STATE_COMPLETED = 'completed';
    public const STATE_CANCELLED = 'cancelled';
}
