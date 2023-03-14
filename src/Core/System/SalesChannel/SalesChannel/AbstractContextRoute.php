<?php declare(strict_types=1);

namespace Laser\Core\System\SalesChannel\SalesChannel;

use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

/**
 * This route can be used to fetch the current context
 * The context contains information about the logged-in user, selected language, selected address etc.
 */
#[Package('core')]
abstract class AbstractContextRoute
{
    abstract public function getDecorated(): AbstractContextRoute;

    abstract public function load(SalesChannelContext $context): ContextLoadRouteResponse;
}
