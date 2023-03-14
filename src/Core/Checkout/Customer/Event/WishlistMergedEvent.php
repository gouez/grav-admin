<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Customer\Event;

use Laser\Core\Framework\Context;
use Laser\Core\Framework\Event\LaserSalesChannelEvent;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('customer-order')]
class WishlistMergedEvent extends Event implements LaserSalesChannelEvent
{
    /**
     * @var array
     */
    protected $products;

    /**
     * @var SalesChannelContext
     */
    protected $context;

    public function __construct(
        array $product,
        SalesChannelContext $context
    ) {
        $this->products = $product;
        $this->context = $context;
    }

    public function getProducts(): array
    {
        return $this->products;
    }

    public function getContext(): Context
    {
        return $this->context->getContext();
    }

    public function getSalesChannelContext(): SalesChannelContext
    {
        return $this->context;
    }
}
