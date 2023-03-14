<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\SearchKeyword;

use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\Search\Term\SearchPattern;
use Laser\Core\Framework\Log\Package;

#[Package('inventory')]
interface ProductSearchTermInterpreterInterface
{
    public function interpret(string $word, Context $context): SearchPattern;
}
