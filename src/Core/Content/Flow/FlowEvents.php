<?php declare(strict_types=1);

namespace Laser\Core\Content\Flow;

use Laser\Core\Content\Flow\Events\FlowIndexerEvent;
use Laser\Core\Framework\Log\Package;

#[Package('business-ops')]
class FlowEvents
{
    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    final public const FLOW_WRITTEN_EVENT = 'flow.written';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    final public const FLOW_DELETED_EVENT = 'flow.deleted';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    final public const FLOW_LOADED_EVENT = 'flow.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    final public const FLOW_SEARCH_RESULT_LOADED_EVENT = 'flow.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent")
     */
    final public const FLOW_AGGREGATION_LOADED_EVENT = 'flow.aggregation.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityIdSearchResultLoadedEvent")
     */
    final public const FLOW_ID_SEARCH_RESULT_LOADED_EVENT = 'flow.id.search.result.loaded';

    /**
     * @Event("Laser\Core\Content\Flow\Events\FlowIndexerEvent")
     */
    final public const FLOW_INDEXER_EVENT = FlowIndexerEvent::class;

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    final public const FLOW_SEQUENCE_WRITTEN_EVENT = 'flow_sequence.written';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    final public const FLOW_SEQUENCE_DELETED_EVENT = 'flow_sequence.deleted';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    final public const FLOW_SEQUENCE_LOADED_EVENT = 'flow_sequence.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    final public const FLOW_SEQUENCE_SEARCH_RESULT_LOADED_EVENT = 'flow_sequence.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent")
     */
    final public const FLOW_SEQUENCE_AGGREGATION_LOADED_EVENT = 'flow_sequence.aggregation.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityIdSearchResultLoadedEvent")
     */
    final public const FLOW_SEQUENCE_ID_SEARCH_RESULT_LOADED_EVENT = 'flow_sequence.id.search.result.loaded';
}
