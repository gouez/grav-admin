<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Payment\Cart;

use Laser\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionEntity;
use Laser\Core\Checkout\Order\OrderEntity;
use Laser\Core\Framework\Log\Package;

#[Package('checkout')]
class AsyncPaymentTransactionStruct extends SyncPaymentTransactionStruct
{
    /**
     * @var string
     */
    protected $returnUrl;

    public function __construct(
        OrderTransactionEntity $orderTransaction,
        OrderEntity $order,
        string $returnUrl
    ) {
        parent::__construct($orderTransaction, $order);
        $this->returnUrl = $returnUrl;
    }

    public function getReturnUrl(): string
    {
        return $this->returnUrl;
    }
}
