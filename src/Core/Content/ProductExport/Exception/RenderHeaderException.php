<?php declare(strict_types=1);

namespace Laser\Core\Content\ProductExport\Exception;

use Laser\Core\Framework\Adapter\Twig\Exception\StringTemplateRenderingException;
use Laser\Core\Framework\Log\Package;

#[Package('sales-channel')]
class RenderHeaderException extends StringTemplateRenderingException
{
}
