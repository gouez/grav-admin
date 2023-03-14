<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Document\Renderer;

use Laser\Core\Framework\Log\Package;

#[Package('customer-order')]
final class DocumentRendererConfig
{
    public string $deepLinkCode = '';
}
