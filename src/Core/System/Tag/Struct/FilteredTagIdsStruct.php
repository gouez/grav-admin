<?php declare(strict_types=1);

namespace Laser\Core\System\Tag\Struct;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\Struct;

#[Package('business-ops')]
class FilteredTagIdsStruct extends Struct
{
    public function __construct(
        protected array $ids,
        protected int $total
    ) {
    }

    public function getIds(): array
    {
        return $this->ids;
    }

    public function getTotal(): int
    {
        return $this->total;
    }
}
