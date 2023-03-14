<?php declare(strict_types=1);

namespace Laser\Core\Framework\Script\Execution\Awareness;

use Laser\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
interface StoppableHook
{
    public function stopPropagation(): void;

    public function isPropagationStopped(): bool;
}
