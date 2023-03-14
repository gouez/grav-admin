<?php declare(strict_types=1);

namespace Laser\Core\System\Salutation\SalesChannel;

use Laser\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\StoreApiResponse;
use Laser\Core\System\Salutation\SalutationCollection;

#[Package('customer-order')]
class SalutationRouteResponse extends StoreApiResponse
{
    /**
     * @var EntitySearchResult
     */
    protected $object;

    public function __construct(EntitySearchResult $object)
    {
        parent::__construct($object);
    }

    public function getSalutations(): SalutationCollection
    {
        /** @var SalutationCollection $collection */
        $collection = $this->object->getEntities();

        return $collection;
    }
}
