<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\SalesChannel\FindVariant;

use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;

/**
 * This route is used the search for a matching product variant by given options
 */
#[Package('inventory')]
abstract class AbstractFindProductVariantRoute
{
    abstract public function getDecorated(): AbstractFindProductVariantRoute;

    abstract public function load(string $productId, Request $request, SalesChannelContext $context): FindProductVariantRouteResponse;
}
