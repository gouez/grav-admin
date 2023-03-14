<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\Hook;

use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Script\Execution\Awareness\SalesChannelContextAware;

/**
 * @internal Not intended for use in plugins
 * Can be implemented by hooks to provide services with the sales channel context.
 * The services can inject the context beforehand and provide a narrow API to the developer.
 */
#[Package('checkout')]
interface CartAware extends SalesChannelContextAware
{
    public function getCart(): Cart;
}
