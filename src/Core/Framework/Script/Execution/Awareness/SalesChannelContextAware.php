<?php declare(strict_types=1);

namespace Laser\Core\Framework\Script\Execution\Awareness;

use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

/**
 * Can be implemented by hooks to provide services with the sales channel context.
 * The services can inject the context beforehand and provide a narrow API to the developer.
 *
 * @internal
 */
#[Package('core')]
interface SalesChannelContextAware
{
    public function getSalesChannelContext(): SalesChannelContext;
}
