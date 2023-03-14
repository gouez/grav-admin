<?php declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer\Search;

use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Laser\Core\Framework\DataAbstractionLayer\Search\AggregationResult\AggregationResultCollection;
use Laser\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
interface EntityAggregatorInterface
{
    public function aggregate(EntityDefinition $definition, Criteria $criteria, Context $context): AggregationResultCollection;
}
