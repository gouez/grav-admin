<?php declare(strict_types=1);

namespace Laser\Core\Content\Rule;

use Laser\Core\Content\Rule\Event\RuleIndexerEvent;
use Laser\Core\Framework\Log\Package;

#[Package('business-ops')]
class RuleEvents
{
    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    final public const RULE_WRITTEN_EVENT = 'rule.written';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    final public const RULE_DELETED_EVENT = 'rule.deleted';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    final public const RULE_LOADED_EVENT = 'rule.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    final public const RULE_SEARCH_RESULT_LOADED_EVENT = 'rule.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent")
     */
    final public const RULE_AGGREGATION_LOADED_EVENT = 'rule.aggregation.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityIdSearchResultLoadedEvent")
     */
    final public const RULE_ID_SEARCH_RESULT_LOADED_EVENT = 'rule.id.search.result.loaded';

    /**
     * @Event("Laser\Core\Content\Rule\Event\RuleIndexerEvent")
     */
    final public const RULE_INDEXER_EVENT = RuleIndexerEvent::class;
}
