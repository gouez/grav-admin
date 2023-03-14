<?php declare(strict_types=1);

namespace Laser\Core\Content\Seo\Hreflang;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\StructCollection;

/**
 * @extends StructCollection<HreflangStruct>
 */
#[Package('sales-channel')]
class HreflangCollection extends StructCollection
{
    public function getApiAlias(): string
    {
        return 'seo_hreflang_collection';
    }

    protected function getExpectedClass(): string
    {
        return HreflangStruct::class;
    }
}
