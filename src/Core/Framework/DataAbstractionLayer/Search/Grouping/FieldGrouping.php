<?php declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer\Search\Grouping;

use Laser\Core\Framework\DataAbstractionLayer\Search\CriteriaPartInterface;
use Laser\Core\Framework\Log\Package;

/**
 * @final
 */
#[Package('core')]
class FieldGrouping implements CriteriaPartInterface
{
    public function __construct(private readonly string $field)
    {
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getFields(): array
    {
        return [$this->field];
    }
}
