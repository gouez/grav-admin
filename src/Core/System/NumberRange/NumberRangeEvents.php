<?php declare(strict_types=1);

namespace Laser\Core\System\NumberRange;

use Laser\Core\Framework\Log\Package;

#[Package('checkout')]
class NumberRangeEvents
{
    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    final public const NUMBER_RANGE_WRITTEN_EVENT = 'number_range.written';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    final public const NUMBER_RANGE_DELETED_EVENT = 'number_range.deleted';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    final public const NUMBER_RANGE_LOADED_EVENT = 'number_range.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    final public const NUMBER_RANGE_SEARCH_RESULT_LOADED_EVENT = 'number_range.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent")
     */
    final public const NUMBER_RANGE_AGGREGATION_LOADED_EVENT = 'number_range.aggregation.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityIdSearchResultLoadedEvent")
     */
    final public const NUMBER_RANGE_ID_SEARCH_RESULT_LOADED_EVENT = 'number_range.id.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    final public const NUMBER_RANGE_STATE_WRITTEN_EVENT = 'number_range_state.written';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    final public const NUMBER_RANGE_STATE_DELETED_EVENT = 'number_range_state.deleted';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    final public const NUMBER_RANGE_STATE_LOADED_EVENT = 'number_range_state.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    final public const NUMBER_RANGE_STATE_SEARCH_RESULT_LOADED_EVENT = 'number_range_state.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent")
     */
    final public const NUMBER_RANGE_STATE_AGGREGATION_LOADED_EVENT = 'number_range_state.aggregation.result.loaded';

    /**
     * @Event("Laser\Core\System\NumberRange\ValueGenerator\NumberRangeGeneratedEvent")
     */
    final public const NUMBER_RANGE_GENERATED = 'number_range.generated';
}
