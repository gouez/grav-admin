<?php declare(strict_types=1);

namespace Laser\Core\Content\Flow\Dispatching\Aware;

use Laser\Core\Framework\Event\FlowEventAware;
use Laser\Core\Framework\Log\Package;

#[Package('business-ops')]
interface CustomerRecoveryAware extends FlowEventAware
{
    public const CUSTOMER_RECOVERY_ID = 'customerRecoveryId';

    public const CUSTOMER_RECOVERY = 'customerRecovery';

    public function getCustomerRecoveryId(): string;
}
