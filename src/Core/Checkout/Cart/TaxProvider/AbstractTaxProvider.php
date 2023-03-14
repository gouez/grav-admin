<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\TaxProvider;

use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Checkout\Cart\TaxProvider\Struct\TaxProviderResult;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('checkout')]
abstract class AbstractTaxProvider
{
    abstract public function provide(Cart $cart, SalesChannelContext $context): TaxProviderResult;
}
