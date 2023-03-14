<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\Script\Execution;

use Laser\Core\Framework\Script\Execution\Awareness\StoppableHook;
use Laser\Core\Framework\Script\Execution\Awareness\StoppableHookTrait;

/**
 * @internal
 */
class StoppableTestHook extends TestHook implements StoppableHook
{
    use StoppableHookTrait;
}
