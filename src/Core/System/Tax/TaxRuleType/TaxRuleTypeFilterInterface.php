<?php declare(strict_types=1);

namespace Laser\Core\System\Tax\TaxRuleType;

use Laser\Core\Checkout\Cart\Delivery\Struct\ShippingLocation;
use Laser\Core\Checkout\Customer\CustomerEntity;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\Tax\Aggregate\TaxRule\TaxRuleEntity;

#[Package('customer-order')]
interface TaxRuleTypeFilterInterface
{
    public function match(TaxRuleEntity $taxRuleEntity, ?CustomerEntity $customer, ShippingLocation $shippingLocation): bool;
}
