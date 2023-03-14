<?php declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer\Search\Aggregation\Bucket;

use Laser\Core\Framework\DataAbstractionLayer\Search\Aggregation\Aggregation;
use Laser\Core\Framework\Log\Package;

#[Package('core')]
class BucketAggregation extends Aggregation
{
    public function __construct(
        string $name,
        string $field,
        protected ?Aggregation $aggregation
    ) {
        parent::__construct($name, $field);
    }

    public function getFields(): array
    {
        if (!$this->aggregation) {
            return [$this->field];
        }

        $fields = $this->aggregation->getFields();
        $fields[] = $this->field;

        return $fields;
    }

    public function getAggregation(): ?Aggregation
    {
        return $this->aggregation;
    }
}
