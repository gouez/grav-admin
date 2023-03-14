<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\SalesChannel\Detail;

use Laser\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Laser\Core\Content\Property\PropertyGroupCollection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\ArrayStruct;
use Laser\Core\System\SalesChannel\StoreApiResponse;

#[Package('inventory')]
class ProductDetailRouteResponse extends StoreApiResponse
{
    /**
     * @var ArrayStruct<string, mixed>
     */
    protected $object;

    public function __construct(
        SalesChannelProductEntity $product,
        ?PropertyGroupCollection $configurator
    ) {
        parent::__construct(new ArrayStruct([
            'product' => $product,
            'configurator' => $configurator,
        ], 'product_detail'));
    }

    /**
     * @return ArrayStruct<string, mixed>
     */
    public function getResult(): ArrayStruct
    {
        return $this->object;
    }

    public function getProduct(): SalesChannelProductEntity
    {
        return $this->object->get('product');
    }

    public function getConfigurator(): ?PropertyGroupCollection
    {
        return $this->object->get('configurator');
    }
}
