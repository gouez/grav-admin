<?php declare(strict_types=1);

namespace Laser\Core\Framework\App\Payment\Payload\Struct;

use Laser\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionEntity;
use Laser\Core\Framework\Log\Package;

/**
 * @internal only for use by the app-system
 */
#[Package('core')]
interface PaymentPayloadInterface extends SourcedPayloadInterface
{
    public function getOrderTransaction(): OrderTransactionEntity;
}
