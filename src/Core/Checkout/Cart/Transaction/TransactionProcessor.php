<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\Transaction;

use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Laser\Core\Checkout\Cart\Transaction\Struct\Transaction;
use Laser\Core\Checkout\Cart\Transaction\Struct\TransactionCollection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('checkout')]
class TransactionProcessor
{
    public function process(Cart $cart, SalesChannelContext $context): TransactionCollection
    {
        $price = $cart->getPrice()->getTotalPrice();

        return new TransactionCollection([
            new Transaction(
                new CalculatedPrice(
                    $price,
                    $price,
                    $cart->getPrice()->getCalculatedTaxes(),
                    $cart->getPrice()->getTaxRules()
                ),
                $context->getPaymentMethod()->getId()
            ),
        ]);
    }
}
