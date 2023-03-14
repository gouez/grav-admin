<?php declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer\Search\Aggregation\Bucket;

use Laser\Core\Framework\DataAbstractionLayer\Search\Aggregation\Aggregation;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\Filter;
use Laser\Core\Framework\Log\Package;

/**
 * @final
 */
#[Package('core')]
class FilterAggregation extends BucketAggregation
{
    /**
     * @param Filter[] $filter
     */
    public function __construct(
        string $name,
        Aggregation $aggregation,
        private array $filter
    ) {
        parent::__construct($name, '', $aggregation);
    }

    /**
     * @return Filter[]
     */
    public function getFilter(): array
    {
        return $this->filter;
    }

    public function getFields(): array
    {
        $fields = $this->aggregation?->getFields() ?? [];

        foreach ($this->filter as $filter) {
            $nested = $filter->getFields();
            foreach ($nested as $field) {
                $fields[] = $field;
            }
        }

        return $fields;
    }

    /**
     * @param Filter[] $filters
     */
    public function addFilters(array $filters): void
    {
        foreach ($filters as $filter) {
            $this->filter[] = $filter;
        }
    }
}
