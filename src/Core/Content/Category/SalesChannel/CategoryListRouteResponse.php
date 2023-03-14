<?php declare(strict_types=1);

namespace Laser\Core\Content\Category\SalesChannel;

use Laser\Core\Content\Category\CategoryCollection;
use Laser\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\StoreApiResponse;

#[Package('content')]
class CategoryListRouteResponse extends StoreApiResponse
{
    /**
     * @var EntitySearchResult
     */
    protected $object;

    public function __construct(EntitySearchResult $object)
    {
        parent::__construct($object);
    }

    public function getCategories(): CategoryCollection
    {
        /** @var CategoryCollection $categories */
        $categories = $this->object->getEntities();

        return $categories;
    }
}
