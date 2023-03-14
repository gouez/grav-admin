<?php declare(strict_types=1);

namespace Laser\Core\Content\Newsletter\SalesChannel;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Validation\DataBag\RequestDataBag;
use Laser\Core\System\SalesChannel\NoContentResponse;
use Laser\Core\System\SalesChannel\SalesChannelContext;

/**
 * This route is used to confirm the newsletter registration
 * The required parameters are: "hash" (received from the mail) and "email"
 */
#[Package('customer-order')]
abstract class AbstractNewsletterConfirmRoute
{
    abstract public function getDecorated(): AbstractNewsletterConfirmRoute;

    abstract public function confirm(RequestDataBag $dataBag, SalesChannelContext $context): NoContentResponse;
}
