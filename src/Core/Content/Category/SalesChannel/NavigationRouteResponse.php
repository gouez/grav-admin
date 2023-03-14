<?php declare(strict_types=1);

namespace Laser\Core\Content\Category\SalesChannel;

use Laser\Core\Content\Category\CategoryCollection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\StoreApiResponse;

#[Package('content')]
class NavigationRouteResponse extends StoreApiResponse
{
    /**
     * @var CategoryCollection
     */
    protected $object;

    public function __construct(CategoryCollection $categories)
    {
        parent::__construct($categories);
    }

    public function getCategories(): CategoryCollection
    {
        return $this->object;
    }
}
