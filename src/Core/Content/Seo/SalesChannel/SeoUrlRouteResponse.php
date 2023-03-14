<?php declare(strict_types=1);

namespace Laser\Core\Content\Seo\SalesChannel;

use Laser\Core\Content\Seo\SeoUrl\SeoUrlCollection;
use Laser\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\StoreApiResponse;

#[Package('sales-channel')]
class SeoUrlRouteResponse extends StoreApiResponse
{
    /**
     * @var EntitySearchResult
     */
    protected $object;

    public function __construct(EntitySearchResult $object)
    {
        parent::__construct($object);
    }

    public function getSeoUrls(): SeoUrlCollection
    {
        /** @var SeoUrlCollection $collection */
        $collection = $this->object->getEntities();

        return $collection;
    }
}
