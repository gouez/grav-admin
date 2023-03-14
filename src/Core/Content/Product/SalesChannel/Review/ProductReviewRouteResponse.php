<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\SalesChannel\Review;

use Laser\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\StoreApiResponse;

#[Package('inventory')]
class ProductReviewRouteResponse extends StoreApiResponse
{
    /**
     * @var EntitySearchResult
     */
    protected $object;

    public function __construct(EntitySearchResult $object)
    {
        parent::__construct($object);
    }

    public function getResult(): EntitySearchResult
    {
        return $this->object;
    }
}
