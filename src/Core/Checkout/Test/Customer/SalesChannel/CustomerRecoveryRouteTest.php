<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Customer\SalesChannel;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\Customer\Exception\CustomerNotFoundByHashException;
use Laser\Core\Checkout\Customer\SalesChannel\CustomerRecoveryIsExpiredRoute;
use Laser\Core\Defaults;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Plugin\Exception\DecorationPatternException;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Laser\Core\Framework\Util\Random;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\Framework\Validation\DataBag\RequestDataBag;
use Laser\Core\Framework\Validation\Exception\ConstraintViolationException;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextFactory;
use Laser\Core\Test\TestDefaults;

/**
 * @internal
 *
 * @group store-api
 */
#[Package('customer-order')]
class CustomerRecoveryRouteTest extends TestCase
{
    use IntegrationTestBehaviour;
    use CustomerTestTrait;

    private string $hash;

    private string $hashId;

    protected function setUp(): void
    {
        $email = Uuid::randomHex() . '@example.com';
        $customerId = $this->createCustomer('laser', $email);

        $this->hash = Random::getAlphanumericString(32);
        $this->hashId = Uuid::randomHex();

        $this->getContainer()->get('customer_recovery.repository')->create([
            [
                'id' => $this->hashId,
                'customerId' => $customerId,
                'hash' => $this->hash,
            ],
        ], Context::createDefaultContext());
    }

    public function testNotDecorated(): void
    {
        $customerRecoveryRoute = $this->getContainer()->get(CustomerRecoveryIsExpiredRoute::class);

        static::expectException(DecorationPatternException::class);
        $customerRecoveryRoute->getDecorated();
    }

    public function testGetCustomerRecoveryNotFound(): void
    {
        $customerRecoveryRoute = $this->getContainer()->get(CustomerRecoveryIsExpiredRoute::class);

        $token = Uuid::randomHex();

        $context = $this->getContainer()->get(SalesChannelContextFactory::class)->create($token, TestDefaults::SALES_CHANNEL);

        static::expectException(CustomerNotFoundByHashException::class);
        $customerRecoveryRoute->load(new RequestDataBag(['hash' => Random::getAlphanumericString(32)]), $context);
    }

    public function testGetCustomerRecoveryInvalidHash(): void
    {
        $customerRecoveryRoute = $this->getContainer()->get(CustomerRecoveryIsExpiredRoute::class);

        $token = Uuid::randomHex();

        $context = $this->getContainer()->get(SalesChannelContextFactory::class)->create($token, TestDefaults::SALES_CHANNEL);

        static::expectException(ConstraintViolationException::class);
        $customerRecoveryRoute->load(new RequestDataBag(['hash' => 'ThisIsAWrongHash']), $context);
    }

    public function testGetCustomerRecovery(): void
    {
        $customerRecoveryRoute = $this->getContainer()->get(CustomerRecoveryIsExpiredRoute::class);

        $token = Uuid::randomHex();

        $context = $this->getContainer()->get(SalesChannelContextFactory::class)->create($token, TestDefaults::SALES_CHANNEL);

        $customerRecoveryResponse = $customerRecoveryRoute->load(new RequestDataBag(['hash' => $this->hash]), $context);

        static::assertFalse($customerRecoveryResponse->isExpired());
    }

    public function testGetCustomerRecoveryExpired(): void
    {
        $customerRecoveryRoute = $this->getContainer()->get(CustomerRecoveryIsExpiredRoute::class);

        $token = Uuid::randomHex();

        $context = $this->getContainer()->get(SalesChannelContextFactory::class)->create($token, TestDefaults::SALES_CHANNEL);

        $this->getContainer()->get(Connection::class)->update(
            'customer_recovery',
            [
                'created_at' => (new \DateTime())->sub(new \DateInterval('PT3H'))->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ],
            [
                'id' => Uuid::fromHexToBytes($this->hashId),
            ]
        );

        $customerRecoveryResponse = $customerRecoveryRoute->load(new RequestDataBag(['hash' => $this->hash]), $context);

        static::assertTrue($customerRecoveryResponse->isExpired());
    }
}
