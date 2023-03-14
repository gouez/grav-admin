<?php declare(strict_types=1);

namespace Laser\Core\Content\Flow\Dispatching;

use Laser\Core\Framework\Log\Package;

/**
 * @internal not intended for decoration or replacement
 */
#[Package('business-ops')]
abstract class AbstractFlowLoader
{
    abstract public function getDecorated(): AbstractFlowLoader;

    abstract public function load(): array;
}
