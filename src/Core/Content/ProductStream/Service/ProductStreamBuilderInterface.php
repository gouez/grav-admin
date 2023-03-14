<?php declare(strict_types=1);

namespace Laser\Core\Content\ProductStream\Service;

use Laser\Core\Framework\Context;
use Laser\Core\Framework\Log\Package;

#[Package('business-ops')]
interface ProductStreamBuilderInterface
{
    public function buildFilters(
        string $id,
        Context $context
    ): array;
}
