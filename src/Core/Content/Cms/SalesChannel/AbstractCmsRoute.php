<?php declare(strict_types=1);

namespace Laser\Core\Content\Cms\SalesChannel;

use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;

#[Package('content
This route can be used to load a single resolved cms page of the authenticated sales channel.')]
abstract class AbstractCmsRoute
{
    abstract public function getDecorated(): AbstractCmsRoute;

    abstract public function load(string $id, Request $request, SalesChannelContext $context): CmsRouteResponse;
}
