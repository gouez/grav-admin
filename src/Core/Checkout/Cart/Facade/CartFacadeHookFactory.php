<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\Facade;

use Laser\Core\Checkout\Cart\Hook\CartAware;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Script\Exception\HookInjectionException;
use Laser\Core\Framework\Script\Execution\Awareness\HookServiceFactory;
use Laser\Core\Framework\Script\Execution\Hook;
use Laser\Core\Framework\Script\Execution\Script;

/**
 * @internal
 */
#[Package('checkout')]
class CartFacadeHookFactory extends HookServiceFactory
{
    public function __construct(private readonly CartFacadeHelper $helper)
    {
    }

    public function factory(Hook $hook, Script $script): CartFacade
    {
        if (!$hook instanceof CartAware) {
            throw new HookInjectionException($hook, self::class, CartAware::class);
        }

        return new CartFacade($this->helper, $hook->getCart(), $hook->getSalesChannelContext());
    }

    /**
     * @param CartFacade $service
     */
    public function after(object $service, Hook $hook, Script $script): void
    {
        $service->calculate();
    }

    public function getName(): string
    {
        return 'cart';
    }
}
