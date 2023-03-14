<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\Events;

use Laser\Core\Framework\Adapter\Cache\StoreApiRouteCacheTagsEvent;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Laser\Core\System\SalesChannel\StoreApiResponse;
use Symfony\Component\HttpFoundation\Request;

#[Package('inventory')]
class ProductListingRouteCacheTagsEvent extends StoreApiRouteCacheTagsEvent
{
    public function __construct(
        array $tags,
        protected string $categoryId,
        Request $request,
        StoreApiResponse $response,
        SalesChannelContext $context,
        Criteria $criteria
    ) {
        parent::__construct($tags, $request, $response, $context, $criteria);
    }

    public function getCategoryId(): string
    {
        return $this->categoryId;
    }
}
