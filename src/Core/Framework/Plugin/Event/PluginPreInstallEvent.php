<?php declare(strict_types=1);

namespace Laser\Core\Framework\Plugin\Event;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Plugin\Context\InstallContext;
use Laser\Core\Framework\Plugin\PluginEntity;

#[Package('core')]
class PluginPreInstallEvent extends PluginLifecycleEvent
{
    public function __construct(
        PluginEntity $plugin,
        private readonly InstallContext $context
    ) {
        parent::__construct($plugin);
    }

    public function getContext(): InstallContext
    {
        return $this->context;
    }
}
