<?php declare(strict_types=1);

namespace Laser\Core\System\User;

use Laser\Core\Framework\Log\Package;

#[Package('system-settings')]
class UserEvents
{
    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    final public const USER_WRITTEN_EVENT = 'user.written';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    final public const USER_DELETED_EVENT = 'user.deleted';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    final public const USER_LOADED_EVENT = 'user.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    final public const USER_SEARCH_RESULT_LOADED_EVENT = 'user.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent")
     */
    final public const USER_AGGREGATION_LOADED_EVENT = 'user.aggregation.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityIdSearchResultLoadedEvent")
     */
    final public const USER_ID_SEARCH_RESULT_LOADED_EVENT = 'user.id.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    final public const USER_ACCESS_KEY_WRITTEN_EVENT = 'user_access_key.written';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    final public const USER_ACCESS_KEY_DELETED_EVENT = 'user_access_key.deleted';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    final public const USER_ACCESS_KEY_LOADED_EVENT = 'user_access_key.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    final public const USER_ACCESS_KEY_SEARCH_RESULT_LOADED_EVENT = 'user_access_key.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent")
     */
    final public const USER_ACCESS_KEY_AGGREGATION_LOADED_EVENT = 'user_access_key.aggregation.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityIdSearchResultLoadedEvent")
     */
    final public const USER_ACCESS_KEY_ID_SEARCH_RESULT_LOADED_EVENT = 'user_access_key.id.search.result.loaded';
}
