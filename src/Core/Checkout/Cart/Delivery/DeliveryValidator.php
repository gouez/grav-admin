<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\Delivery;

use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Checkout\Cart\CartValidatorInterface;
use Laser\Core\Checkout\Cart\Error\ErrorCollection;
use Laser\Core\Checkout\Shipping\Cart\Error\ShippingMethodBlockedError;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('checkout')]
class DeliveryValidator implements CartValidatorInterface
{
    public function validate(Cart $cart, ErrorCollection $errors, SalesChannelContext $context): void
    {
        foreach ($cart->getDeliveries() as $delivery) {
            $matches = \in_array($delivery->getShippingMethod()->getAvailabilityRuleId(), $context->getRuleIds(), true);

            if ($matches && $delivery->getShippingMethod()->getActive()) {
                continue;
            }

            $errors->add(
                new ShippingMethodBlockedError(
                    (string) $delivery->getShippingMethod()->getTranslation('name')
                )
            );
        }
    }
}
