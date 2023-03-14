<?php declare(strict_types=1);

namespace Laser\Core\Framework\Event;

use Laser\Core\Framework\Log\Package;

#[Package('business-ops')]
interface CustomerAware extends FlowEventAware
{
    public const CUSTOMER_ID = 'customerId';

    public const CUSTOMER = 'customer';

    public function getCustomerId(): string;
}
