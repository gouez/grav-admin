<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\SalesChannel\Search;

use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;

#[Package('system-settings
This route is used for the product search in the search pages')]
abstract class AbstractProductSearchRoute
{
    abstract public function getDecorated(): AbstractProductSearchRoute;

    abstract public function load(Request $request, SalesChannelContext $context, Criteria $criteria): ProductSearchRouteResponse;
}
