<?php declare(strict_types=1);

namespace Laser\Core\Framework\App\Payment\Payload\Struct;

use Laser\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionEntity;
use Laser\Core\Checkout\Order\OrderEntity;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\CloneTrait;
use Laser\Core\Framework\Struct\JsonSerializableTrait;
use Laser\Core\Framework\Struct\Struct;

/**
 * @internal only for use by the app-system
 */
#[Package('core')]
class CapturePayload implements PaymentPayloadInterface
{
    use CloneTrait;
    use JsonSerializableTrait;
    use RemoveAppTrait;

    protected Source $source;

    protected OrderTransactionEntity $orderTransaction;

    public function __construct(
        OrderTransactionEntity $orderTransaction,
        protected OrderEntity $order,
        protected Struct $preOrderPayment
    ) {
        $this->orderTransaction = $this->removeApp($orderTransaction);
    }

    public function setSource(Source $source): void
    {
        $this->source = $source;
    }

    public function getSource(): Source
    {
        return $this->source;
    }

    public function getOrderTransaction(): OrderTransactionEntity
    {
        return $this->orderTransaction;
    }

    public function getOrder(): OrderEntity
    {
        return $this->order;
    }

    public function getPreOrderPayment(): Struct
    {
        return $this->preOrderPayment;
    }
}
