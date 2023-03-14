<?php declare(strict_types=1);

namespace Laser\Core\Framework\Script\Execution;

use Laser\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
interface DeprecatedHook
{
    public static function getDeprecationNotice(): string;
}
