<?php declare(strict_types=1);

namespace Laser\Core\Content\Cms\SalesChannel;

use Laser\Core\Content\Cms\CmsPageEntity;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\StoreApiResponse;

#[Package('content')]
class CmsRouteResponse extends StoreApiResponse
{
    /**
     * @var CmsPageEntity
     */
    protected $object;

    public function __construct(CmsPageEntity $object)
    {
        parent::__construct($object);
    }

    public function getCmsPage(): CmsPageEntity
    {
        return $this->object;
    }
}
