<?php declare(strict_types=1);

namespace Laser\Core\Content\Cms\SalesChannel\Struct;

use Laser\Core\Content\Product\SalesChannel\CrossSelling\CrossSellingElementCollection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\Struct;

#[Package('content')]
class CrossSellingStruct extends Struct
{
    /**
     * @var CrossSellingElementCollection|null
     */
    protected $crossSellings;

    public function getCrossSellings(): ?CrossSellingElementCollection
    {
        return $this->crossSellings;
    }

    public function setCrossSellings(CrossSellingElementCollection $crossSellings): void
    {
        $this->crossSellings = $crossSellings;
    }

    public function getApiAlias(): string
    {
        return 'cms_cross_selling';
    }
}
