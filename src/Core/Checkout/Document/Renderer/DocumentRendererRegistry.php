<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Document\Renderer;

use Laser\Core\Checkout\Document\Exception\InvalidDocumentGeneratorTypeException;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Log\Package;

#[Package('customer-order')]
final class DocumentRendererRegistry
{
    /**
     * @internal
     *
     * @param AbstractDocumentRenderer[] $documentRenderers
     */
    public function __construct(protected iterable $documentRenderers)
    {
    }

    public function render(string $documentType, array $operations, Context $context, DocumentRendererConfig $rendererConfig): RendererResult
    {
        foreach ($this->documentRenderers as $documentRenderer) {
            if ($documentRenderer->supports() !== $documentType) {
                continue;
            }

            return $documentRenderer->render($operations, $context, $rendererConfig);
        }

        throw new InvalidDocumentGeneratorTypeException($documentType);
    }
}
