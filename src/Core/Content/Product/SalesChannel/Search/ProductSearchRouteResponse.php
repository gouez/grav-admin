<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\SalesChannel\Search;

use Laser\Core\Content\Product\SalesChannel\Listing\ProductListingResult;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\StoreApiResponse;

#[Package('system-settings')]
class ProductSearchRouteResponse extends StoreApiResponse
{
    /**
     * @var ProductListingResult
     */
    protected $object;

    public function getListingResult(): ProductListingResult
    {
        return $this->object;
    }
}
