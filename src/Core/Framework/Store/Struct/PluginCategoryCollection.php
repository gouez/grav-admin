<?php declare(strict_types=1);

namespace Laser\Core\Framework\Store\Struct;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\Collection;

/**
 * @codeCoverageIgnore
 * Pseudo immutable collection
 *
 * @extends Collection<PluginCategoryStruct>
 */
#[Package('merchant-services')]
final class PluginCategoryCollection extends Collection
{
    public function getExpectedClass(): string
    {
        return PluginCategoryStruct::class;
    }

    public function getApiAlias(): string
    {
        return 'store_category_collection';
    }
}
