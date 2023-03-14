<?php declare(strict_types=1);

namespace Laser\Core\System\Country\SalesChannel;

use Laser\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\Country\CountryCollection;
use Laser\Core\System\SalesChannel\StoreApiResponse;

#[Package('system-settings')]
class CountryRouteResponse extends StoreApiResponse
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

    public function getCountries(): CountryCollection
    {
        /** @var CountryCollection $collection */
        $collection = $this->object->getEntities();

        return $collection;
    }
}
