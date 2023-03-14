<?php declare(strict_types=1);

namespace Laser\Core\System\Salutation\SalesChannel;

use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;

/**
 * This route can be used to load all salutation of the authenticated sales channel.
 * With this route it is also possible to send the standard API parameters such as: 'page', 'limit', 'filter', etc.
 */
#[Package('customer-order')]
abstract class AbstractSalutationRoute
{
    abstract public function getDecorated(): AbstractSalutationRoute;

    abstract public function load(Request $request, SalesChannelContext $context, Criteria $criteria): SalutationRouteResponse;
}
