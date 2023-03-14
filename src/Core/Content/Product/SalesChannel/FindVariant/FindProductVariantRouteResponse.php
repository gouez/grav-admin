<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\SalesChannel\FindVariant;

use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\StoreApiResponse;

#[Package('inventory')]
class FindProductVariantRouteResponse extends StoreApiResponse
{
    /**
     * @var FoundCombination
     */
    protected $object;

    public function __construct(FoundCombination $object)
    {
        parent::__construct($object);
    }

    public function getFoundCombination(): FoundCombination
    {
        return $this->object;
    }
}
