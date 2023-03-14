<?php declare(strict_types=1);

namespace Laser\Core\Framework\App\Cms;

use Laser\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('content')]
abstract class AbstractBlockTemplateLoader
{
    abstract public function getTemplateForBlock(CmsExtensions $cmsExtensions, string $blockName): string;

    abstract public function getStylesForBlock(CmsExtensions $cmsExtensions, string $blockName): string;
}
