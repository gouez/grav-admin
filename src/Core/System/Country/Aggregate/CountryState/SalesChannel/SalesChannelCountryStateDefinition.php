<?php declare(strict_types=1);

namespace Laser\Core\System\Country\Aggregate\CountryState\SalesChannel;

use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\Country\Aggregate\CountryState\CountryStateDefinition;
use Laser\Core\System\SalesChannel\Entity\SalesChannelDefinitionInterface;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('system-settings')]
class SalesChannelCountryStateDefinition extends CountryStateDefinition implements SalesChannelDefinitionInterface
{
    public function processCriteria(Criteria $criteria, SalesChannelContext $context): void
    {
        $criteria->addFilter(
            new EqualsFilter('country_state.country.salesChannels.id', $context->getSalesChannel()->getId())
        );
    }
}
