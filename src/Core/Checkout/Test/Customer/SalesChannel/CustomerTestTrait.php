<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Customer\SalesChannel;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Laser\Core\Framework\Test\TestCaseBase\SalesChannelApiTestBehaviour;
use Laser\Core\Framework\Util\Random;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextPersister;
use Laser\Core\Test\TestDefaults;

#[Package('customer-order')]
trait CustomerTestTrait
{
    use IntegrationTestBehaviour;
    use SalesChannelApiTestBehaviour;

    private function getLoggedInContextToken(string $customerId, string $salesChannelId = TestDefaults::SALES_CHANNEL): string
    {
        $token = Random::getAlphanumericString(32);
        $this->getContainer()->get(SalesChannelContextPersister::class)->save(
            $token,
            [
                'customerId' => $customerId,
                'billingAddressId' => null,
                'shippingAddressId' => null,
            ],
            $salesChannelId,
            $customerId
        );

        return $token;
    }
}
