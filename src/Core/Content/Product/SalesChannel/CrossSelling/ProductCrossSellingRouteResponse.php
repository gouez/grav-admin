<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\SalesChannel\CrossSelling;

use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\StoreApiResponse;

#[Package('inventory')]
class ProductCrossSellingRouteResponse extends StoreApiResponse
{
    /**
     * @var CrossSellingElementCollection
     */
    protected $object;

    public function __construct(CrossSellingElementCollection $object)
    {
        parent::__construct($object);
    }

    public function getResult(): CrossSellingElementCollection
    {
        return $this->object;
    }
}
