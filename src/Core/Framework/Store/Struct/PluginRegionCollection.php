<?php declare(strict_types=1);

namespace Laser\Core\Framework\Store\Struct;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\Collection;

/**
 * @codeCoverageIgnore
 * Pseudo immutable collection
 *
 * @extends Collection<PluginRegionStruct>
 */
#[Package('merchant-services')]
final class PluginRegionCollection extends Collection
{
    public function getExpectedClass(): string
    {
        return PluginRegionStruct::class;
    }

    public function getApiAlias(): string
    {
        return 'store_plugin_region_collection';
    }
}
