<?php declare(strict_types=1);

namespace Laser\Core\Framework\Script\Api;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Script\Execution\Awareness\HookServiceFactory;
use Laser\Core\Framework\Script\Execution\Awareness\SalesChannelContextAware;
use Laser\Core\Framework\Script\Execution\Hook;
use Laser\Core\Framework\Script\Execution\Script;
use Laser\Storefront\Controller\ScriptController;
use Symfony\Component\Routing\RouterInterface;

/**
 * @internal
 */
#[Package('core')]
class ScriptResponseFactoryFacadeHookFactory extends HookServiceFactory
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly ?ScriptController $scriptController
    ) {
    }

    public function factory(Hook $hook, Script $script): ScriptResponseFactoryFacade
    {
        $salesChannelContext = null;
        if ($hook instanceof SalesChannelContextAware) {
            $salesChannelContext = $hook->getSalesChannelContext();
        }

        return new ScriptResponseFactoryFacade(
            $this->router,
            $this->scriptController,
            $salesChannelContext
        );
    }

    public function getName(): string
    {
        return 'response';
    }
}
