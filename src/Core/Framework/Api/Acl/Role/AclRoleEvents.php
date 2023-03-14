<?php declare(strict_types=1);

namespace Laser\Core\Framework\Api\Acl\Role;

use Laser\Core\Framework\Log\Package;

#[Package('core')]
class AclRoleEvents
{
    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    final public const ACL_ROLE_WRITTEN_EVENT = 'acl_role.written';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    final public const ACL_ROLE_DELETED_EVENT = 'acl_role.deleted';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    final public const ACL_ROLE_LOADED_EVENT = 'acl_role.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    final public const ACL_ROLE_SEARCH_RESULT_LOADED_EVENT = 'acl_role.search.result.loaded';
}
