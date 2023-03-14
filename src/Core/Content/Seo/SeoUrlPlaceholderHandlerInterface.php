<?php declare(strict_types=1);

namespace Laser\Core\Content\Seo;

use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('sales-channel')]
interface SeoUrlPlaceholderHandlerInterface
{
    /**
     * @param string $name
     */
    public function generate($name, array $parameters = []): string;

    public function replace(string $content, string $host, SalesChannelContext $context): string;
}
