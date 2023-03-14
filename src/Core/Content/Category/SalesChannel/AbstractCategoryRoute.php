<?php declare(strict_types=1);

namespace Laser\Core\Content\Category\SalesChannel;

use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;

#[Package('content
This route can be used to a singled category with resolved cms page of the authenticated sales channel.
It is also possible to use "home" as navigationId to load the start page.')]
abstract class AbstractCategoryRoute
{
    abstract public function getDecorated(): AbstractCategoryRoute;

    abstract public function load(string $navigationId, Request $request, SalesChannelContext $context): CategoryRouteResponse;
}
