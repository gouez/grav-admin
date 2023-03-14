<?php declare(strict_types=1);

namespace Laser\Core\Framework\Plugin\Event;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Plugin\Context\UninstallContext;
use Laser\Core\Framework\Plugin\PluginEntity;

#[Package('core')]
class PluginPreUninstallEvent extends PluginLifecycleEvent
{
    public function __construct(
        PluginEntity $plugin,
        private readonly UninstallContext $context
    ) {
        parent::__construct($plugin);
    }

    public function getContext(): UninstallContext
    {
        return $this->context;
    }
}
