<?php declare(strict_types=1);

namespace Laser\Core\Content\LandingPage\SalesChannel;

use Laser\Core\Content\LandingPage\LandingPageEntity;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\StoreApiResponse;

#[Package('content')]
class LandingPageRouteResponse extends StoreApiResponse
{
    /**
     * @var LandingPageEntity
     */
    protected $object;

    public function __construct(LandingPageEntity $landingPage)
    {
        parent::__construct($landingPage);
    }

    public function getLandingPage(): LandingPageEntity
    {
        return $this->object;
    }
}
