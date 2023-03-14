<?php declare(strict_types=1);

namespace Laser\Core\Content\ProductExport\Service;

use Laser\Core\Content\ProductExport\ProductExportEntity;
use Laser\Core\Content\ProductExport\Struct\ExportBehavior;
use Laser\Core\Content\ProductExport\Struct\ProductExportResult;
use Laser\Core\Framework\Log\Package;

#[Package('sales-channel')]
interface ProductExportGeneratorInterface
{
    public function generate(
        ProductExportEntity $productExport,
        ExportBehavior $exportBehavior
    ): ?ProductExportResult;
}
