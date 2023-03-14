<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Payment\DataAbstractionLayer;

use Laser\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexingMessage;
use Laser\Core\Framework\Log\Package;

#[Package('checkout')]
class PaymentMethodIndexingMessage extends EntityIndexingMessage
{
}
