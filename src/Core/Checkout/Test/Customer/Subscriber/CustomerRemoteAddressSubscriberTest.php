<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Customer\Subscriber;

use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\Customer\CustomerEntity;
use Laser\Core\Checkout\Test\Cart\LineItem\Group\Helpers\Traits\LineItemTestFixtureBehaviour;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Test\TestCaseBase\SalesChannelFunctionalTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\PlatformRequest;
use Laser\Core\Test\TestDefaults;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\IpUtils;

/**
 * @internal
 */
#[Package('customer-order')]
class CustomerRemoteAddressSubscriberTest extends TestCase
{
    use SalesChannelFunctionalTestBehaviour;
    use LineItemTestFixtureBehaviour;

    private KernelBrowser $browser;

    public function setUp(): void
    {
        $this->browser = $this->createCustomSalesChannelBrowser([
            'id' => TestDefaults::SALES_CHANNEL,
            'languages' => [],
        ]);
        $this->assignSalesChannelContext($this->browser);
    }

    public function testUpdateRemoteAddressByLogin(): void
    {
        $email = Uuid::randomHex() . '@example.com';
        $password = 'laser';

        $customerId = $this->login($email, $password);

        $remoteAddress = $this->browser->getRequest()->getClientIp();

        $customer = $this->fetchCustomerById($customerId);

        static::assertNotSame($customer->getRemoteAddress(), $remoteAddress);
        static::assertSame($customer->getRemoteAddress(), IpUtils::anonymize((string) $remoteAddress));
    }

    private function login(string $email, string $password): string
    {
        $customerId = $this->createCustomer($password, $email);

        $this->browser->request('POST', '/store-api/account/login', [
            'username' => $email,
            'password' => $password,
        ]);

        $response = $this->browser->getResponse();

        // After login successfully, the context token will be set in the header
        $contextToken = $response->headers->get(PlatformRequest::HEADER_CONTEXT_TOKEN) ?? '';
        static::assertNotEmpty($contextToken);

        $this->browser->setServerParameter('HTTP_SW_CONTEXT_TOKEN', $contextToken);

        return $customerId;
    }

    private function createCustomer(string $password, string $email): string
    {
        $customerId = Uuid::randomHex();
        $addressId = Uuid::randomHex();

        $this->getContainer()->get('customer.repository')->create([
            [
                'id' => $customerId,
                'salesChannelId' => TestDefaults::SALES_CHANNEL,
                'defaultShippingAddress' => [
                    'id' => $addressId,
                    'firstName' => 'Max',
                    'lastName' => 'Mustermann',
                    'street' => 'Musterstraße 1',
                    'city' => 'Schoöppingen',
                    'zipcode' => '12345',
                    'salutationId' => $this->getValidSalutationId(),
                    'countryId' => $this->getValidCountryId(),
                ],
                'defaultBillingAddressId' => $addressId,
                'defaultPaymentMethodId' => $this->getValidPaymentMethodId(),
                'groupId' => TestDefaults::FALLBACK_CUSTOMER_GROUP,
                'email' => $email,
                'password' => $password,
                'firstName' => 'Max',
                'lastName' => 'Mustermann',
                'salutationId' => $this->getValidSalutationId(),
                'customerNumber' => '12345',
            ],
        ], Context::createDefaultContext());

        return $customerId;
    }

    private function fetchCustomerById(string $customerId): CustomerEntity
    {
        return $this->getContainer()->get('customer.repository')
            ->search(new Criteria([$customerId]), Context::createDefaultContext())
            ->first();
    }
}
