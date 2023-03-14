<?php declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer\Write\Command;

use Laser\Core\Framework\Api\Acl\Role\AclRoleDefinition;
use Laser\Core\Framework\Log\Package;

#[Package('core')]
class UpdateCommand extends WriteCommand implements ChangeSetAware
{
    use ChangeSetAwareTrait;

    public function getPrivilege(): ?string
    {
        return AclRoleDefinition::PRIVILEGE_UPDATE;
    }
}
