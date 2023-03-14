<?php declare(strict_types=1);

namespace Laser\Core\System\NumberRange\Aggregate\NumberRangeSalesChannel;

use Laser\Core\Framework\DataAbstractionLayer\EntityCollection;
use Laser\Core\Framework\Log\Package;

/**
 * @extends EntityCollection<NumberRangeSalesChannelEntity>
 */
#[Package('checkout')]
class NumberRangeSalesChannelCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'number_range_sales_channel_collection';
    }

    protected function getExpectedClass(): string
    {
        return NumberRangeSalesChannelEntity::class;
    }
}
