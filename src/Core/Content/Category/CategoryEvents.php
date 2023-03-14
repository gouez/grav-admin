<?php declare(strict_types=1);

namespace Laser\Core\Content\Category;

use Laser\Core\Content\Category\Event\CategoryIndexerEvent;
use Laser\Core\Framework\Log\Package;

#[Package('content')]
class CategoryEvents
{
    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    final public const CATEGORY_WRITTEN_EVENT = 'category.written';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    final public const CATEGORY_DELETED_EVENT = 'category.deleted';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    final public const CATEGORY_LOADED_EVENT = 'category.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    final public const CATEGORY_SEARCH_RESULT_LOADED_EVENT = 'category.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent")
     */
    final public const CATEGORY_AGGREGATION_LOADED_EVENT = 'category.aggregation.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityIdSearchResultLoadedEvent")
     */
    final public const CATEGORY_ID_SEARCH_RESULT_LOADED_EVENT = 'category.id.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    final public const CATEGORY_TRANSLATION_WRITTEN_EVENT = 'category_translation.written';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    final public const CATEGORY_TRANSLATION_DELETED_EVENT = 'category_translation.deleted';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    final public const CATEGORY_TRANSLATION_LOADED_EVENT = 'category_translation.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    final public const CATEGORY_TRANSLATION_SEARCH_RESULT_LOADED_EVENT = 'category_translation.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent")
     */
    final public const CATEGORY_TRANSLATION_AGGREGATION_LOADED_EVENT = 'category_translation.aggregation.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityIdSearchResultLoadedEvent")
     */
    final public const CATEGORY_TRANSLATION_ID_SEARCH_RESULT_LOADED_EVENT = 'category_translation.id.search.result.loaded';

    /**
     * @Event("Laser\Core\Content\Category\Event\CategoryIndexerEvent")
     */
    final public const CATEGORY_INDEXER_EVENT = CategoryIndexerEvent::class;
}
