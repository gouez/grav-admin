<?php declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer\Search\Sorting;

use Laser\Core\Framework\Log\Package;

/**
 * @final
 */
#[Package('core')]
class CountSorting extends FieldSorting
{
    protected string $type = 'count';
}
