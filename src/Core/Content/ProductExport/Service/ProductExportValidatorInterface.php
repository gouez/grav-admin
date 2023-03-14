<?php declare(strict_types=1);

namespace Laser\Core\Content\ProductExport\Service;

use Laser\Core\Content\ProductExport\ProductExportEntity;
use Laser\Core\Framework\Log\Package;

#[Package('sales-channel')]
interface ProductExportValidatorInterface
{
    public function validate(ProductExportEntity $productExportEntity, string $productExportContent): array;
}
