<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Payment\Handler\V630;

use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Checkout\Payment\Cart\PaymentHandler\PreparedPaymentHandlerInterface;
use Laser\Core\Checkout\Payment\Cart\PreparedPaymentTransactionStruct;
use Laser\Core\Checkout\Payment\Exception\CapturePreparedPaymentException;
use Laser\Core\Checkout\Payment\Exception\ValidatePreparedPaymentException;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\ArrayStruct;
use Laser\Core\Framework\Struct\Struct;
use Laser\Core\Framework\Validation\DataBag\RequestDataBag;
use Laser\Core\System\SalesChannel\SalesChannelContext;

/**
 * @internal
 */
#[Package('checkout')]
class PreparedTestPaymentHandler implements PreparedPaymentHandlerInterface
{
    final public const TEST_STRUCT_CONTENT = ['testValue'];

    public static ?Struct $preOrderPaymentStruct = null;

    public static bool $fail = false;

    public function validate(
        Cart $cart,
        RequestDataBag $requestDataBag,
        SalesChannelContext $context
    ): Struct {
        if (self::$fail) {
            throw new ValidatePreparedPaymentException('this is supposed to fail');
        }

        self::$preOrderPaymentStruct = null;

        return new ArrayStruct(self::TEST_STRUCT_CONTENT);
    }

    public function capture(
        PreparedPaymentTransactionStruct $transaction,
        RequestDataBag $requestDataBag,
        SalesChannelContext $context,
        Struct $preOrderPaymentStruct
    ): void {
        if (self::$fail) {
            throw new CapturePreparedPaymentException($transaction->getOrderTransaction()->getId(), 'this is supposed to fail');
        }

        self::$preOrderPaymentStruct = $preOrderPaymentStruct;
    }
}
