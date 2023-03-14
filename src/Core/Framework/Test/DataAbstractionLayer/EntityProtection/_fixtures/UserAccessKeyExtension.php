<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\DataAbstractionLayer\EntityProtection\_fixtures;

use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityExtension;
use Laser\Core\Framework\DataAbstractionLayer\EntityProtection\EntityProtectionCollection;
use Laser\Core\Framework\DataAbstractionLayer\EntityProtection\ReadProtection;
use Laser\Core\Framework\DataAbstractionLayer\EntityProtection\WriteProtection;
use Laser\Core\System\User\Aggregate\UserAccessKey\UserAccessKeyDefinition;

/**
 * @internal
 */
class UserAccessKeyExtension extends EntityExtension
{
    /**
     * {@inheritdoc}
     */
    public function getDefinitionClass(): string
    {
        return UserAccessKeyDefinition::class;
    }

    public function extendProtections(EntityProtectionCollection $protections): void
    {
        $protections->add(new ReadProtection(Context::SYSTEM_SCOPE, Context::USER_SCOPE));
        $protections->add(new WriteProtection(Context::SYSTEM_SCOPE, Context::USER_SCOPE));
    }
}
