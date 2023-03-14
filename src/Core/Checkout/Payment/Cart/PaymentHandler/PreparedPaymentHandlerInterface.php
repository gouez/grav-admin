<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Payment\Cart\PaymentHandler;

use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Checkout\Payment\Cart\PreparedPaymentTransactionStruct;
use Laser\Core\Checkout\Payment\Exception\CapturePreparedPaymentException;
use Laser\Core\Checkout\Payment\Exception\ValidatePreparedPaymentException;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\Struct;
use Laser\Core\Framework\Validation\DataBag\RequestDataBag;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('checkout')]
interface PreparedPaymentHandlerInterface extends PaymentHandlerInterface
{
    /**
     * The validate method will be called before actually placing the order.
     * It allows the validation of the supplied payment transaction.
     *
     * @throws ValidatePreparedPaymentException
     */
    public function validate(
        Cart $cart,
        RequestDataBag $requestDataBag,
        SalesChannelContext $context
    ): Struct;

    /**
     * The capture method will be called, after successfully validating the payment before
     *
     * @throws CapturePreparedPaymentException
     */
    public function capture(
        PreparedPaymentTransactionStruct $transaction,
        RequestDataBag $requestDataBag,
        SalesChannelContext $context,
        Struct $preOrderPaymentStruct
    ): void;
}
