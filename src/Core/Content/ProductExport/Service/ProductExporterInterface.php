<?php declare(strict_types=1);

namespace Laser\Core\Content\ProductExport\Service;

use Laser\Core\Content\ProductExport\Exception\ExportInvalidException;
use Laser\Core\Content\ProductExport\Exception\ExportNotFoundException;
use Laser\Core\Content\ProductExport\Struct\ExportBehavior;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('sales-channel')]
interface ProductExporterInterface
{
    /**
     * @throws ExportInvalidException
     * @throws ExportNotFoundException
     */
    public function export(
        SalesChannelContext $context,
        ExportBehavior $behavior,
        ?string $productExportId = null
    ): void;
}
