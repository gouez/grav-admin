<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Shipping\SalesChannel;

use Laser\Core\Checkout\Shipping\ShippingMethodDefinition;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\Entity\SalesChannelDefinitionInterface;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('checkout')]
class SalesChannelShippingMethodDefinition extends ShippingMethodDefinition implements SalesChannelDefinitionInterface
{
    public function processCriteria(Criteria $criteria, SalesChannelContext $context): void
    {
        $criteria->addFilter(new EqualsFilter('shipping_method.salesChannels.id', $context->getSalesChannel()->getId()));
    }
}
