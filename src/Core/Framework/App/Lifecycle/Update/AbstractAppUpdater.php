<?php declare(strict_types=1);

namespace Laser\Core\Framework\App\Lifecycle\Update;

use Laser\Core\Framework\Context;
use Laser\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
abstract class AbstractAppUpdater
{
    abstract public function updateApps(Context $context): void;

    abstract protected function getDecorated(): AbstractAppUpdater;
}
