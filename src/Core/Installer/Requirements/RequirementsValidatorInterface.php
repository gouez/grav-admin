<?php declare(strict_types=1);

namespace Laser\Core\Installer\Requirements;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Installer\Requirements\Struct\RequirementsCheckCollection;

/**
 * @internal
 */
#[Package('core')]
interface RequirementsValidatorInterface
{
    public function validateRequirements(RequirementsCheckCollection $checks): RequirementsCheckCollection;
}
