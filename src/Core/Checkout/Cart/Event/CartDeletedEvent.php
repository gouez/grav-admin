<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\Event;

use Laser\Core\Framework\Context;
use Laser\Core\Framework\Event\LaserSalesChannelEvent;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('checkout')]
class CartDeletedEvent extends Event implements LaserSalesChannelEvent
{
    /**
     * @var SalesChannelContext
     */
    protected $context;

    public function __construct(SalesChannelContext $context)
    {
        $this->context = $context;
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
