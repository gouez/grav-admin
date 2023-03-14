<?php declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer\Search\Term\Filter;

use Laser\Core\Framework\Context;
use Laser\Core\Framework\Log\Package;

#[Package('core')]
abstract class AbstractTokenFilter
{
    abstract public function getDecorated(): AbstractTokenFilter;

    abstract public function filter(array $tokens, Context $context): array;
}
