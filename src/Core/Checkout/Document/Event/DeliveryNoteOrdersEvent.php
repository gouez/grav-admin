<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Document\Event;

use Laser\Core\Framework\Log\Package;

#[Package('customer-order')]
final class DeliveryNoteOrdersEvent extends DocumentOrderEvent
{
}
