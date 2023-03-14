<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart;

use Laser\Core\Checkout\Cart\Delivery\DeliveryProcessor;
use Laser\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('checkout')]
abstract class AbstractCartPersister
{
    abstract public function getDecorated(): AbstractCartPersister;

    abstract public function load(string $token, SalesChannelContext $context): Cart;

    abstract public function save(Cart $cart, SalesChannelContext $context): void;

    abstract public function delete(string $token, SalesChannelContext $context): void;

    abstract public function replace(string $oldToken, string $newToken, SalesChannelContext $context): void;

    protected function shouldPersist(Cart $cart): bool
    {
        return $cart->getLineItems()->count() > 0
            || $cart->getAffiliateCode() !== null
            || $cart->getCampaignCode() !== null
            || $cart->getCustomerComment() !== null
            || $cart->getExtension(DeliveryProcessor::MANUAL_SHIPPING_COSTS) instanceof CalculatedPrice;
    }
}
