<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Payment\Cart;

use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Checkout\Cart\CartValidatorInterface;
use Laser\Core\Checkout\Cart\Error\ErrorCollection;
use Laser\Core\Checkout\Payment\Cart\Error\PaymentMethodBlockedError;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('checkout')]
class PaymentMethodValidator implements CartValidatorInterface
{
    public function validate(Cart $cart, ErrorCollection $errors, SalesChannelContext $context): void
    {
        $paymentMethod = $context->getPaymentMethod();
        if (!$paymentMethod->getActive()) {
            $errors->add(
                new PaymentMethodBlockedError((string) $paymentMethod->getTranslation('name'), 'inactive')
            );
        }

        $ruleId = $paymentMethod->getAvailabilityRuleId();

        if ($ruleId && !\in_array($ruleId, $context->getRuleIds(), true)) {
            $errors->add(
                new PaymentMethodBlockedError((string) $paymentMethod->getTranslation('name'), 'rule not matching')
            );
        }

        if (!\in_array($paymentMethod->getId(), $context->getSalesChannel()->getPaymentMethodIds() ?? [], true)) {
            $errors->add(
                new PaymentMethodBlockedError((string) $paymentMethod->getTranslation('name'), 'not allowed')
            );
        }
    }
}
