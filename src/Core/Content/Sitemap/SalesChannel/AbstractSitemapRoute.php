<?php declare(strict_types=1);

namespace Laser\Core\Content\Sitemap\SalesChannel;

use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;

#[Package('sales-channel')]
abstract class AbstractSitemapRoute
{
    abstract public function load(Request $request, SalesChannelContext $context): SitemapRouteResponse;

    abstract public function getDecorated(): AbstractSitemapRoute;
}
