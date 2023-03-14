<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Order\SalesChannel;

use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SuccessResponse;

#[Package('customer-order')]
class SetPaymentOrderRouteResponse extends SuccessResponse
{
}
