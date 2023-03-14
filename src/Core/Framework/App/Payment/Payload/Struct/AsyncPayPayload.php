<?php declare(strict_types=1);

namespace Laser\Core\Framework\App\Payment\Payload\Struct;

use Laser\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionEntity;
use Laser\Core\Checkout\Order\OrderEntity;
use Laser\Core\Framework\Log\Package;

/**
 * @internal only for use by the app-system
 */
#[Package('core')]
class AsyncPayPayload extends SyncPayPayload
{
    public function __construct(
        OrderTransactionEntity $orderTransaction,
        OrderEntity $order,
        protected string $returnUrl,
        protected array $requestData
    ) {
        parent::__construct($orderTransaction, $order);
    }

    public function getReturnUrl(): string
    {
        return $this->returnUrl;
    }

    public function getRequestData(): array
    {
        return $this->requestData;
    }
}
