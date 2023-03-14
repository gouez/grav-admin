<?php declare(strict_types=1);

namespace Laser\Core\Content\Category\SalesChannel;

use Laser\Core\Content\Category\CategoryEntity;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\StoreApiResponse;

#[Package('content')]
class CategoryRouteResponse extends StoreApiResponse
{
    /**
     * @var CategoryEntity
     */
    protected $object;

    public function __construct(CategoryEntity $category)
    {
        parent::__construct($category);
    }

    public function getCategory(): CategoryEntity
    {
        return $this->object;
    }
}
