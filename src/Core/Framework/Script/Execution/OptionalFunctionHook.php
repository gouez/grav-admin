<?php declare(strict_types=1);

namespace Laser\Core\Framework\Script\Execution;

use Laser\Core\Framework\Log\Package;

/**
 * Marker that a function does not need to be implemented by a script
 *
 * @internal
 */
#[Package('core')]
abstract class OptionalFunctionHook extends FunctionHook
{
    public static function willBeRequiredInVersion(): ?string
    {
        return null;
    }
}
