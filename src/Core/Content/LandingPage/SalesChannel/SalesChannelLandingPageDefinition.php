<?php declare(strict_types=1);

namespace Laser\Core\Content\LandingPage\SalesChannel;

use Laser\Core\Content\LandingPage\LandingPageDefinition;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\Entity\SalesChannelDefinitionInterface;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('content')]
class SalesChannelLandingPageDefinition extends LandingPageDefinition implements SalesChannelDefinitionInterface
{
    public function processCriteria(Criteria $criteria, SalesChannelContext $context): void
    {
    }
}
