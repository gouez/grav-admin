<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Customer;

use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\Customer\CustomerEntity;
use Laser\Core\Checkout\Customer\Event\CustomerBeforeLoginEvent;
use Laser\Core\Checkout\Customer\Event\CustomerChangedPaymentMethodEvent;
use Laser\Core\Checkout\Customer\Event\CustomerLoginEvent;
use Laser\Core\Checkout\Customer\Event\CustomerLogoutEvent;
use Laser\Core\Checkout\Customer\Exception\BadCredentialsException;
use Laser\Core\Checkout\Customer\SalesChannel\AccountService;
use Laser\Core\Checkout\Customer\SalesChannel\ChangePaymentMethodRoute;
use Laser\Core\Checkout\Customer\SalesChannel\LoginRoute;
use Laser\Core\Checkout\Customer\SalesChannel\LogoutRoute;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Test\TestCaseBase\SalesChannelFunctionalTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\Framework\Validation\DataBag\DataBag;
use Laser\Core\Framework\Validation\DataBag\RequestDataBag;
use Laser\Core\System\SalesChannel\Context\AbstractSalesChannelContextFactory;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextFactory;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Laser\Core\Test\TestDefaults;
use Symfony\Component\HttpKernel\Debug\TraceableEventDispatcher;

/**
 * @internal
 */
#[Package('customer-order')]
class AccountServiceEventTest extends TestCase
{
    use SalesChannelFunctionalTestBehaviour;

    private AccountService $accountService;

    private EntityRepository $customerRepository;

    private SalesChannelContext $salesChannelContext;

    private LoginRoute $loginRoute;

    private LogoutRoute $logoutRoute;

    private ChangePaymentMethodRoute $changePaymentMethodRoute;

    protected function setUp(): void
    {
        $this->accountService = $this->getContainer()->get(AccountService::class);
        $this->customerRepository = $this->getContainer()->get('customer.repository');
        $this->logoutRoute = $this->getContainer()->get(LogoutRoute::class);
        $this->changePaymentMethodRoute = $this->getContainer()->get(ChangePaymentMethodRoute::class);
        $this->loginRoute = $this->getContainer()->get(LoginRoute::class);

        /** @var AbstractSalesChannelContextFactory $salesChannelContextFactory */
        $salesChannelContextFactory = $this->getContainer()->get(SalesChannelContextFactory::class);
        $this->salesChannelContext = $salesChannelContextFactory->create(Uuid::randomHex(), TestDefaults::SALES_CHANNEL);

        $this->createCustomer('laser', 'info@example.com');
    }

    public function testLoginBeforeEventNotDispatchedIfNoCredentialsGiven(): void
    {
        /** @var TraceableEventDispatcher $dispatcher */
        $dispatcher = $this->getContainer()->get('event_dispatcher');

        $eventDidRun = false;

        $listenerClosure = $this->getEmailListenerClosure($eventDidRun, $this);
        $this->addEventListener($dispatcher, CustomerBeforeLoginEvent::class, $listenerClosure);

        $dataBag = new DataBag();
        $dataBag->add([
            'username' => '',
            'password' => 'laser',
        ]);

        try {
            $this->loginRoute->login($dataBag->toRequestDataBag(), $this->salesChannelContext);
        } catch (BadCredentialsException) {
            // nth
        }
        static::assertFalse($eventDidRun, 'Event "' . CustomerBeforeLoginEvent::class . '" did run');

        $eventDidRun = false;

        try {
            $this->accountService->login('', $this->salesChannelContext);
        } catch (BadCredentialsException) {
            // nth
        }
        static::assertFalse($eventDidRun, 'Event "' . CustomerBeforeLoginEvent::class . '" did run');

        $dispatcher->removeListener(CustomerBeforeLoginEvent::class, $listenerClosure);
    }

    public function testLoginEventsDispatched(): void
    {
        /** @var TraceableEventDispatcher $dispatcher */
        $dispatcher = $this->getContainer()->get('event_dispatcher');

        $eventsToTest = [
            CustomerBeforeLoginEvent::class,
            CustomerLoginEvent::class,
        ];

        foreach ($eventsToTest as $eventClass) {
            $eventDidRun = false;

            switch ($eventClass) {
                case CustomerBeforeLoginEvent::class:
                    $listenerClosure = $this->getEmailListenerClosure($eventDidRun, $this);

                    break;
                case CustomerLoginEvent::class:
                default:
                    $listenerClosure = $this->getCustomerListenerClosure($eventDidRun, $this);
            }

            $this->addEventListener($dispatcher, $eventClass, $listenerClosure);

            $dataBag = new DataBag();
            $dataBag->add([
                'username' => 'info@example.com',
                'password' => 'laser',
            ]);

            $this->loginRoute->login($dataBag->toRequestDataBag(), $this->salesChannelContext);
            static::assertTrue($eventDidRun, 'Event "' . $eventClass . '" did not run');

            $eventDidRun = false;

            $this->accountService->login('info@example.com', $this->salesChannelContext);
            /** @phpstan-ignore-next-line - $eventDidRun updated value on listener */
            static::assertTrue($eventDidRun, 'Event "' . $eventClass . '" did not run');

            $dispatcher->removeListener($eventClass, $listenerClosure);
        }
    }

    public function testLogoutEventsDispatched(): void
    {
        $email = 'info@example.com';
        /** @var TraceableEventDispatcher $dispatcher */
        $dispatcher = $this->getContainer()->get('event_dispatcher');

        $eventDidRun = false;

        $listenerClosure = $this->getCustomerListenerClosure($eventDidRun, $this);
        $this->addEventListener($dispatcher, CustomerLogoutEvent::class, $listenerClosure);

        $customer = $this->customerRepository->search(
            (new Criteria())->addFilter(new EqualsFilter('email', $email)),
            $this->salesChannelContext->getContext()
        )->first();

        $this->salesChannelContext->assign(['customer' => $customer]);

        static::assertNotNull($customer = $this->salesChannelContext->getCustomer());
        static::assertSame($email, $customer->getEmail());

        $this->logoutRoute->logout($this->salesChannelContext, new RequestDataBag());

        static::assertTrue($eventDidRun, 'Event "' . CustomerLogoutEvent::class . '" did not run');

        $dispatcher->removeListener(CustomerLogoutEvent::class, $listenerClosure);
    }

    public function testChangeDefaultPaymentMethod(): void
    {
        $email = 'info@example.com';
        /** @var TraceableEventDispatcher $dispatcher */
        $dispatcher = $this->getContainer()->get('event_dispatcher');

        $eventDidRun = false;

        $listenerClosure = $this->getCustomerListenerClosure($eventDidRun, $this);
        $this->addEventListener($dispatcher, CustomerChangedPaymentMethodEvent::class, $listenerClosure);

        /** @var CustomerEntity $customer */
        $customer = $this->customerRepository->search(
            (new Criteria())->addFilter(new EqualsFilter('email', $email)),
            $this->salesChannelContext->getContext()
        )->first();

        $this->salesChannelContext->assign(['customer' => $customer]);

        static::assertNotNull($customer = $this->salesChannelContext->getCustomer());
        static::assertSame($email, $customer->getEmail());

        $this->changePaymentMethodRoute->change(
            $customer->getDefaultPaymentMethodId(),
            new RequestDataBag(),
            $this->salesChannelContext,
            $customer
        );
        static::assertTrue($eventDidRun, 'Event "' . CustomerChangedPaymentMethodEvent::class . '" did not run');

        $dispatcher->removeListener(CustomerChangedPaymentMethodEvent::class, $listenerClosure);
    }

    private function getEmailListenerClosure(bool &$eventDidRun, self $phpunit): callable
    {
        return function ($event) use (&$eventDidRun, $phpunit): void {
            $eventDidRun = true;
            $phpunit->assertSame('info@example.com', $event->getEmail());
        };
    }

    private function getCustomerListenerClosure(bool &$eventDidRun, self $phpunit): callable
    {
        return function ($event) use (&$eventDidRun, $phpunit): void {
            $eventDidRun = true;
            $phpunit->assertSame('info@example.com', $event->getCustomer()->getEmail());
        };
    }
}
