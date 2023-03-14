<?php declare(strict_types=1);

namespace Laser\Core\Content\ProductExport\Struct;

use Laser\Core\Content\ProductExport\Error\Error;
use Laser\Core\Framework\Log\Package;

#[Package('sales-channel')]
class ProductExportResult
{
    /**
     * @param Error[] $errors
     */
    public function __construct(
        private readonly string $content,
        private readonly array $errors,
        private readonly int $total
    ) {
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return \count($this->errors) > 0;
    }

    public function getTotal(): int
    {
        return $this->total;
    }
}
