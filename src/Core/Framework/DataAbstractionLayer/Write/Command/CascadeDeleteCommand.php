<?php declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer\Write\Command;

use Laser\Core\Framework\Log\Package;

/**
 * @final
 */
#[Package('core')]
class CascadeDeleteCommand extends DeleteCommand
{
    public function isValid(): bool
    {
        // prevent execution
        return false;
    }

    public function getPrivilege(): ?string
    {
        return null;
    }
}
