<?php declare(strict_types=1);

namespace Laser\Core\System\Salutation\SalesChannel;

use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\Entity\SalesChannelDefinitionInterface;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Laser\Core\System\Salutation\SalutationDefinition;

#[Package('customer-order')]
class SalesChannelSalutationDefinition extends SalutationDefinition implements SalesChannelDefinitionInterface
{
    public function processCriteria(Criteria $criteria, SalesChannelContext $context): void
    {
    }
}
