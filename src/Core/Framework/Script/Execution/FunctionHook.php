<?php declare(strict_types=1);

namespace Laser\Core\Framework\Script\Execution;

use Laser\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
abstract class FunctionHook extends Hook
{
    /**
     * Returns the name of the function.
     */
    abstract public function getFunctionName(): string;
}
