<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\DataAbstractionLayer\EntityProtection\_fixtures;

use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityExtension;
use Laser\Core\Framework\DataAbstractionLayer\EntityProtection\EntityProtectionCollection;
use Laser\Core\Framework\DataAbstractionLayer\EntityProtection\WriteProtection;
use Laser\Core\System\SystemConfig\SystemConfigDefinition;

/**
 * @internal
 */
class SystemConfigExtension extends EntityExtension
{
    /**
     * {@inheritdoc}
     */
    public function getDefinitionClass(): string
    {
        return SystemConfigDefinition::class;
    }

    public function extendProtections(EntityProtectionCollection $protections): void
    {
        $protections->add(new WriteProtection(Context::SYSTEM_SCOPE, Context::USER_SCOPE));
    }
}
