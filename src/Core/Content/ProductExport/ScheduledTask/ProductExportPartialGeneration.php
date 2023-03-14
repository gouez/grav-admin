<?php declare(strict_types=1);

namespace Laser\Core\Content\ProductExport\ScheduledTask;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\MessageQueue\AsyncMessageInterface;

#[Package('sales-channel')]
class ProductExportPartialGeneration implements AsyncMessageInterface
{
    /**
     * @internal
     */
    public function __construct(
        private readonly string $productExportId,
        private readonly string $salesChannelId,
        private readonly int $offset = 0
    ) {
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getProductExportId(): string
    {
        return $this->productExportId;
    }

    public function getSalesChannelId(): string
    {
        return $this->salesChannelId;
    }
}
