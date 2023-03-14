<?php declare(strict_types=1);

namespace Laser\Core\Framework\App\Event\Hooks;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Script\Execution\TraceHook;

/**
 * @internal
 */
#[Package('core')]
class AppScriptConditionHook extends TraceHook
{
    public static function getServiceIds(): array
    {
        return [];
    }

    public function getName(): string
    {
        return 'rule-conditions';
    }
}
