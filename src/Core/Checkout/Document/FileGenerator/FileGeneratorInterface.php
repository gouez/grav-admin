<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Document\FileGenerator;

use Laser\Core\Checkout\Document\Renderer\RenderedDocument;
use Laser\Core\Framework\Log\Package;

#[Package('customer-order')]
interface FileGeneratorInterface
{
    public function supports(): string;

    public function generate(RenderedDocument $html): string;

    public function getExtension(): string;

    public function getContentType(): string;
}
