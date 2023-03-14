<?php declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer\Dbal;

use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\Filter;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\SingleFieldFilter;
use Laser\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
class JoinGroup extends Filter
{
    /**
     * @param SingleFieldFilter[] $queries
     */
    public function __construct(
        private readonly array $queries,
        private readonly string $path,
        private readonly string $suffix,
        private string $operator
    ) {
    }

    public function getFields(): array
    {
        $fields = [];
        foreach ($this->queries as $query) {
            foreach ($query->getFields() as $field) {
                $fields[] = $field;
            }
        }

        return $fields;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getSuffix(): string
    {
        return $this->suffix;
    }

    public function getOperator(): string
    {
        return $this->operator;
    }

    public function setOperator(string $operator): void
    {
        $this->operator = $operator;
    }

    /**
     * @return SingleFieldFilter[]
     */
    public function getQueries(): array
    {
        return $this->queries;
    }
}
