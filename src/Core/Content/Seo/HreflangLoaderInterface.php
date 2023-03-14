<?php declare(strict_types=1);

namespace Laser\Core\Content\Seo;

use Laser\Core\Content\Seo\Hreflang\HreflangCollection;
use Laser\Core\Framework\Log\Package;

#[Package('sales-channel')]
interface HreflangLoaderInterface
{
    public function load(HreflangLoaderParameter $parameter): HreflangCollection;
}
