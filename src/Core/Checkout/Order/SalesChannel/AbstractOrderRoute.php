<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Order\SalesChannel;

use Laser\Core\Checkout\Cart\Exception\CustomerNotLoggedInException;
use Laser\Core\Checkout\Order\Exception\GuestNotAuthenticatedException;
use Laser\Core\Checkout\Order\Exception\WrongGuestCredentialsException;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;

/**
 * This route is used to load the orders of the logged-in customer
 * With this route it is also possible to send the standard API parameters such as: 'page', 'limit', 'filter', etc.
 */
#[Package('customer-order')]
abstract class AbstractOrderRoute
{
    abstract public function getDecorated(): AbstractOrderRoute;

    /**
     * @throws CustomerNotLoggedInException
     * @throws GuestNotAuthenticatedException
     * @throws WrongGuestCredentialsException
     */
    abstract public function load(Request $request, SalesChannelContext $context, Criteria $criteria): OrderRouteResponse;
}
