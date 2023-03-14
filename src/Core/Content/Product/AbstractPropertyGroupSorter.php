<?php declare(strict_types=1);

namespace Laser\Core\Content\Product;

use Laser\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionEntity;
use Laser\Core\Content\Property\PropertyGroupCollection;
use Laser\Core\Framework\DataAbstractionLayer\EntityCollection;
use Laser\Core\Framework\DataAbstractionLayer\PartialEntity;
use Laser\Core\Framework\Log\Package;

#[Package('inventory')]
abstract class AbstractPropertyGroupSorter
{
    abstract public function getDecorated(): AbstractPropertyGroupSorter;

    /**
     * @param EntityCollection<PropertyGroupOptionEntity|PartialEntity> $options
     */
    abstract public function sort(EntityCollection $options): PropertyGroupCollection;
}
