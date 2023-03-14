<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\SalesChannel\CrossSelling;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\Collection;

/**
 * @extends Collection<CrossSellingElement>
 */
#[Package('inventory')]
class CrossSellingElementCollection extends Collection
{
    public function getExpectedClass(): ?string
    {
        return CrossSellingElement::class;
    }

    public function getApiAlias(): string
    {
        return 'cross_selling_elements';
    }
}
