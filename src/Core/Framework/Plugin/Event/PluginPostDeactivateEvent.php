<?php declare(strict_types=1);

namespace Laser\Core\Framework\Plugin\Event;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Plugin\Context\DeactivateContext;
use Laser\Core\Framework\Plugin\PluginEntity;

#[Package('core')]
class PluginPostDeactivateEvent extends PluginLifecycleEvent
{
    public function __construct(
        PluginEntity $plugin,
        private readonly DeactivateContext $context
    ) {
        parent::__construct($plugin);
    }

    public function getContext(): DeactivateContext
    {
        return $this->context;
    }
}
