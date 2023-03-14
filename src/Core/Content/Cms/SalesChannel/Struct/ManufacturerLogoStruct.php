<?php declare(strict_types=1);

namespace Laser\Core\Content\Cms\SalesChannel\Struct;

use Laser\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerEntity;
use Laser\Core\Framework\Log\Package;

#[Package('content')]
class ManufacturerLogoStruct extends ImageStruct
{
    /**
     * @var ProductManufacturerEntity|null
     */
    protected $manufacturer;

    public function getManufacturer(): ?ProductManufacturerEntity
    {
        return $this->manufacturer;
    }

    public function setManufacturer(?ProductManufacturerEntity $manufacturer): void
    {
        $this->manufacturer = $manufacturer;
    }

    public function getApiAlias(): string
    {
        return 'cms_manufacturer_logo';
    }
}
