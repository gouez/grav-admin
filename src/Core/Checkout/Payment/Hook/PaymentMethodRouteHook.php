<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Payment\Hook;

use Laser\Core\Checkout\Payment\PaymentMethodCollection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Script\Execution\Awareness\SalesChannelContextAwareTrait;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Laser\Core\System\SalesChannel\StoreApiRequestHook;

/**
 * Triggered when PaymentMethodRoute is requested
 *
 * @hook-use-case data_loading
 *
 * @since 6.5.0.0
 */
#[Package('checkout')]
class PaymentMethodRouteHook extends StoreApiRequestHook
{
    use SalesChannelContextAwareTrait;

    final public const HOOK_NAME = 'payment-method-route-request';

    /**
     * @internal
     */
    public function __construct(
        private readonly PaymentMethodCollection $collection,
        private readonly bool $onlyAvailable,
        protected SalesChannelContext $salesChannelContext
    ) {
        parent::__construct($salesChannelContext->getContext());
    }

    public function getName(): string
    {
        return self::HOOK_NAME;
    }

    public function getCollection(): PaymentMethodCollection
    {
        return $this->collection;
    }

    public function isOnlyAvailable(): bool
    {
        return $this->onlyAvailable;
    }
}
