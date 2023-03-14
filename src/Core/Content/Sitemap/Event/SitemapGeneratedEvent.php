<?php declare(strict_types=1);

namespace Laser\Core\Content\Sitemap\Event;

use Laser\Core\Framework\Context;
use Laser\Core\Framework\Event\LaserEvent;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('sales-channel')]
class SitemapGeneratedEvent extends Event implements LaserEvent
{
    public function __construct(private readonly SalesChannelContext $context)
    {
    }

    public function getSalesChannelContext(): SalesChannelContext
    {
        return $this->context;
    }

    public function getContext(): Context
    {
        return $this->context->getContext();
    }
}
