<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\SalesChannel;

use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Plugin\Exception\DecorationPatternException;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('inventory')]
class ProductCloseoutFilterFactory extends AbstractProductCloseoutFilterFactory
{
    public function getDecorated(): AbstractProductCloseoutFilterFactory
    {
        throw new DecorationPatternException(self::class);
    }

    public function create(SalesChannelContext $context): MultiFilter
    {
        return new ProductCloseoutFilter();
    }
}
