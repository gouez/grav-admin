<?php declare(strict_types=1);

namespace Laser\Core\System\SalesChannel\SalesChannel;

use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Laser\Core\System\SalesChannel\StoreApiResponse;

#[Package('core')]
class ContextLoadRouteResponse extends StoreApiResponse
{
    /**
     * @var SalesChannelContext
     */
    protected $object;

    public function __construct(SalesChannelContext $object)
    {
        parent::__construct($object);
    }

    public function getContext(): SalesChannelContext
    {
        return $this->object;
    }
}
