<?php declare(strict_types=1);

namespace Laser\Core\System\SalesChannel\SalesChannel;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Validation\DataBag\RequestDataBag;
use Laser\Core\System\SalesChannel\ContextTokenResponse;
use Laser\Core\System\SalesChannel\SalesChannelContext;

/**
 * This route allows changing configurations inside the context.
 * Following parameters are allowed to change: "currencyId", "languageId", "billingAddressId", "shippingAddressId",
 * "paymentMethodId", "shippingMethodId", "countryId" and "countryStateId"
 */
#[Package('core')]
abstract class AbstractContextSwitchRoute
{
    abstract public function getDecorated(): AbstractContextSwitchRoute;

    abstract public function switchContext(RequestDataBag $data, SalesChannelContext $context): ContextTokenResponse;
}
