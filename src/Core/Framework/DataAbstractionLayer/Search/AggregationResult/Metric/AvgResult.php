<?php declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer\Search\AggregationResult\Metric;

use Laser\Core\Framework\DataAbstractionLayer\Search\AggregationResult\AggregationResult;
use Laser\Core\Framework\Log\Package;

/**
 * @final
 */
#[Package('core')]
class AvgResult extends AggregationResult
{
    public function __construct(
        string $name,
        protected float $avg
    ) {
        parent::__construct($name);
    }

    public function getAvg(): float
    {
        return $this->avg;
    }
}
