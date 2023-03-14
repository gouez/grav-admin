<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\Hook;

use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Checkout\Cart\Facade\CartFacadeHookFactory;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Script\Execution\Hook;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Laser\Core\System\SystemConfig\Facade\SystemConfigFacadeHookFactory;

/**
 * @package checkout
 */
/**
 * Triggered during the cart calculation process.
 *
 * @hook-use-case cart_manipulation
 *
 * @since 6.4.8.0
 */
#[Package('checkout')]
class CartHook extends Hook implements CartAware
{
    final public const HOOK_NAME = 'cart';

    private readonly SalesChannelContext $salesChannelContext;

    /**
     * @internal
     */
    public function __construct(
        private readonly Cart $cart,
        SalesChannelContext $context
    ) {
        parent::__construct($context->getContext());
        $this->salesChannelContext = $context;
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }

    public static function getServiceIds(): array
    {
        return [
            CartFacadeHookFactory::class,
            SystemConfigFacadeHookFactory::class,
        ];
    }

    public function getName(): string
    {
        return self::HOOK_NAME;
    }

    public function getSalesChannelContext(): SalesChannelContext
    {
        return $this->salesChannelContext;
    }
}
