<?php declare(strict_types=1);

namespace Laser\Core\System\Country\SalesChannel;

use Laser\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\Country\Aggregate\CountryState\CountryStateCollection;
use Laser\Core\System\SalesChannel\StoreApiResponse;

#[Package('system-settings')]
class CountryStateRouteResponse extends StoreApiResponse
{
    /**
     * @var EntitySearchResult
     */
    protected $object;

    public function __construct(EntitySearchResult $object)
    {
        parent::__construct($object);
    }

    public function getStates(): CountryStateCollection
    {
        /** @var CountryStateCollection $countryStateCollection */
        $countryStateCollection = $this->object->getEntities();

        return $countryStateCollection;
    }
}
