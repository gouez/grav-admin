<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\SalesChannel\Listing;

use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\StoreApiResponse;

#[Package('inventory')]
class ProductListingRouteResponse extends StoreApiResponse
{
    /**
     * @var ProductListingResult
     */
    protected $object;

    public function __construct(ProductListingResult $object)
    {
        parent::__construct($object);
    }

    public function getResult(): ProductListingResult
    {
        return $this->object;
    }
}
