<?php declare(strict_types=1);

namespace Laser\Core\Content\Newsletter\SalesChannel;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Validation\DataBag\RequestDataBag;
use Laser\Core\System\SalesChannel\NoContentResponse;
use Laser\Core\System\SalesChannel\SalesChannelContext;

/**
 * This route is used to unsubscribe the newsletter
 * The required parameters is "email"
 */
#[Package('customer-order')]
abstract class AbstractNewsletterUnsubscribeRoute
{
    abstract public function getDecorated(): AbstractNewsletterUnsubscribeRoute;

    abstract public function unsubscribe(RequestDataBag $dataBag, SalesChannelContext $context): NoContentResponse;
}
