<?php declare(strict_types=1);

namespace Laser\Core\Framework\Plugin\Event;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Plugin\Context\ActivateContext;
use Laser\Core\Framework\Plugin\PluginEntity;

#[Package('core')]
class PluginPreActivateEvent extends PluginLifecycleEvent
{
    public function __construct(
        PluginEntity $plugin,
        private readonly ActivateContext $context
    ) {
        parent::__construct($plugin);
    }

    public function getContext(): ActivateContext
    {
        return $this->context;
    }
}
