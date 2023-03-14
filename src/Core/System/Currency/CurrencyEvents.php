<?php declare(strict_types=1);

namespace Laser\Core\System\Currency;

use Laser\Core\Framework\Log\Package;

#[Package('inventory')]
class CurrencyEvents
{
    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    final public const CURRENCY_WRITTEN_EVENT = 'currency.written';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    final public const CURRENCY_DELETED_EVENT = 'currency.deleted';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    final public const CURRENCY_LOADED_EVENT = 'currency.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    final public const CURRENCY_SEARCH_RESULT_LOADED_EVENT = 'currency.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent")
     */
    final public const CURRENCY_AGGREGATION_LOADED_EVENT = 'currency.aggregation.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityIdSearchResultLoadedEvent")
     */
    final public const CURRENCY_ID_SEARCH_RESULT_LOADED_EVENT = 'currency.id.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    final public const CURRENCY_TRANSLATION_WRITTEN_EVENT = 'currency_translation.written';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    final public const CURRENCY_TRANSLATION_DELETED_EVENT = 'currency_translation.deleted';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    final public const CURRENCY_TRANSLATION_LOADED_EVENT = 'currency_translation.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    final public const CURRENCY_TRANSLATION_SEARCH_RESULT_LOADED_EVENT = 'currency_translation.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent")
     */
    final public const CURRENCY_TRANSLATION_AGGREGATION_LOADED_EVENT = 'currency_translation.aggregation.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityIdSearchResultLoadedEvent")
     */
    final public const CURRENCY_TRANSLATION_ID_SEARCH_RESULT_LOADED_EVENT = 'currency_translation.id.search.result.loaded';
}
