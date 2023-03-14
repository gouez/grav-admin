<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Payment\Cart;

use Laser\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionEntity;
use Laser\Core\Checkout\Order\OrderEntity;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\CloneTrait;
use Laser\Core\Framework\Struct\ExtendableInterface;
use Laser\Core\Framework\Struct\ExtendableTrait;
use Laser\Core\Framework\Struct\JsonSerializableTrait;

#[Package('checkout')]
class SyncPaymentTransactionStruct implements \JsonSerializable, ExtendableInterface
{
    use CloneTrait;
    use JsonSerializableTrait;
    use ExtendableTrait;

    /**
     * @var OrderTransactionEntity
     */
    protected $orderTransaction;

    /**
     * @var OrderEntity
     */
    protected $order;

    public function __construct(
        OrderTransactionEntity $orderTransaction,
        OrderEntity $order
    ) {
        $this->orderTransaction = $orderTransaction;
        $this->order = $order;
    }

    public function getOrderTransaction(): OrderTransactionEntity
    {
        return $this->orderTransaction;
    }

    public function getOrder(): OrderEntity
    {
        return $this->order;
    }
}
