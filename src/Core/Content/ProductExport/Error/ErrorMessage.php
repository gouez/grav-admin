<?php declare(strict_types=1);

namespace Laser\Core\Content\ProductExport\Error;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\Struct;

#[Package('sales-channel')]
class ErrorMessage extends Struct
{
    /**
     * @var string
     */
    protected $message;

    /**
     * @var int|null
     */
    protected $line;

    /**
     * @var int|null
     */
    protected $column;

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getLine(): ?int
    {
        return $this->line;
    }

    public function getColumn(): ?int
    {
        return $this->column;
    }

    public function getApiAlias(): string
    {
        return 'product_export_error_message';
    }
}
