<?php declare(strict_types=1);

namespace Laser\Core\System\Unit;

use Laser\Core\Framework\Log\Package;

#[Package('inventory')]
class UnitEvents
{
    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    final public const UNIT_WRITTEN_EVENT = 'unit.written';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    final public const UNIT_DELETED_EVENT = 'unit.deleted';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    final public const UNIT_LOADED_EVENT = 'unit.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    final public const UNIT_SEARCH_RESULT_LOADED_EVENT = 'unit.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent")
     */
    final public const UNIT_AGGREGATION_LOADED_EVENT = 'unit.aggregation.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityIdSearchResultLoadedEvent")
     */
    final public const UNIT_ID_SEARCH_RESULT_LOADED_EVENT = 'unit.id.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    final public const UNIT_TRANSLATION_WRITTEN_EVENT = 'unit_translation.written';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    final public const UNIT_TRANSLATION_DELETED_EVENT = 'unit_translation.deleted';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    final public const UNIT_TRANSLATION_LOADED_EVENT = 'unit_translation.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    final public const UNIT_TRANSLATION_SEARCH_RESULT_LOADED_EVENT = 'unit_translation.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent")
     */
    final public const UNIT_TRANSLATION_AGGREGATION_LOADED_EVENT = 'unit_translation.aggregation.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityIdSearchResultLoadedEvent")
     */
    final public const UNIT_TRANSLATION_ID_SEARCH_RESULT_LOADED_EVENT = 'unit_translation.id.search.result.loaded';
}
