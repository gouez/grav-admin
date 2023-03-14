<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\Events;

use Laser\Core\Framework\Log\Package;

#[Package('inventory')]
class ProductSearchResultEvent extends ProductListingResultEvent
{
}
