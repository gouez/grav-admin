<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Document\Renderer;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\Struct;

#[Package('customer-order')]
final class RendererResult extends Struct
{
    /**
     * @var RenderedDocument[]
     */
    private array $success = [];

    /**
     * @var \Throwable[]
     */
    private array $errors = [];

    public function addSuccess(string $orderId, RenderedDocument $renderedDocument): void
    {
        $this->success[$orderId] = $renderedDocument;
    }

    public function addError(string $orderId, \Throwable $exception): void
    {
        $this->errors[$orderId] = $exception;
    }

    public function getSuccess(): array
    {
        return $this->success;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
