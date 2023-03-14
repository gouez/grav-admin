<?php declare(strict_types=1);

namespace Laser\Core\Framework\Script\Execution;

use Laser\Core\Framework\Log\Package;

/**
 * Only to be used by "dummy" hooks for the sole purpose of tracing
 *
 * @internal
 */
#[Package('core')]
abstract class TraceHook extends Hook
{
}
