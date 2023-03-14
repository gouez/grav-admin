<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Shipping\Hook;

use Laser\Core\Checkout\Shipping\ShippingMethodCollection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Script\Execution\Awareness\SalesChannelContextAwareTrait;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Laser\Core\System\SalesChannel\StoreApiRequestHook;

/**
 * Triggered when ShippingMethodRoute is requested
 *
 * @hook-use-case data_loading
 *
 * @since 6.5.0.0
 */
#[Package('checkout')]
class ShippingMethodRouteHook extends StoreApiRequestHook
{
    use SalesChannelContextAwareTrait;

    final public const HOOK_NAME = 'shipping-method-route-request';

    /**
     * @internal
     */
    public function __construct(
        private readonly ShippingMethodCollection $collection,
        private readonly bool $onlyAvailable,
        protected SalesChannelContext $salesChannelContext
    ) {
        parent::__construct($salesChannelContext->getContext());
    }

    public function getName(): string
    {
        return self::HOOK_NAME;
    }

    public function getCollection(): ShippingMethodCollection
    {
        return $this->collection;
    }

    public function isOnlyAvailable(): bool
    {
        return $this->onlyAvailable;
    }
}
