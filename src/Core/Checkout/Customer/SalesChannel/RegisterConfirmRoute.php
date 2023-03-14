<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Customer\SalesChannel;

use Laser\Core\Checkout\Customer\CustomerEntity;
use Laser\Core\Checkout\Customer\Event\CustomerLoginEvent;
use Laser\Core\Checkout\Customer\Event\CustomerRegisterEvent;
use Laser\Core\Checkout\Customer\Event\GuestCustomerRegisterEvent;
use Laser\Core\Checkout\Customer\Exception\CustomerAlreadyConfirmedException;
use Laser\Core\Checkout\Customer\Exception\CustomerNotFoundByHashException;
use Laser\Core\Checkout\Customer\Exception\NoHashProvidedException;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Laser\Core\Framework\Feature;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Plugin\Exception\DecorationPatternException;
use Laser\Core\Framework\Validation\DataBag\RequestDataBag;
use Laser\Core\Framework\Validation\DataValidationDefinition;
use Laser\Core\Framework\Validation\DataValidator;
use Laser\Core\PlatformRequest;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextPersister;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextServiceInterface;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextServiceParameters;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Route(defaults: ['_routeScope' => ['store-api']])]
#[Package('customer-order')]
class RegisterConfirmRoute extends AbstractRegisterConfirmRoute
{
    /**
     * @internal
     */
    public function __construct(
        private readonly EntityRepository $customerRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly DataValidator $validator,
        private readonly SalesChannelContextPersister $contextPersister,
        private readonly SalesChannelContextServiceInterface $contextService
    ) {
    }

    public function getDecorated(): AbstractRegisterConfirmRoute
    {
        throw new DecorationPatternException(self::class);
    }

    #[Route(path: '/store-api/account/register-confirm', name: 'store-api.account.register.confirm', methods: ['POST'])]
    public function confirm(RequestDataBag $dataBag, SalesChannelContext $context): CustomerResponse
    {
        if (!$dataBag->has('hash')) {
            throw new NoHashProvidedException();
        }

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('hash', $dataBag->get('hash')));
        $criteria->addAssociation('addresses');
        $criteria->addAssociation('salutation');
        $criteria->setLimit(1);

        $customer = $this->customerRepository
            ->search($criteria, $context->getContext())
            ->first();

        if ($customer === null) {
            throw new CustomerNotFoundByHashException($dataBag->get('hash'));
        }

        $this->validator->validate(
            [
                'em' => $dataBag->get('em'),
                'doubleOptInRegistration' => $customer->getDoubleOptInRegistration(),
            ],
            $this->getBeforeConfirmValidation(hash('sha1', (string) $customer->getEmail()))
        );

        if ((!Feature::isActive('v6.6.0.0') && $customer->getActive())
            || $customer->getDoubleOptInConfirmDate() !== null) {
            throw new CustomerAlreadyConfirmedException($customer->getId());
        }

        $customerUpdate = [
            'id' => $customer->getId(),
            'doubleOptInConfirmDate' => new \DateTimeImmutable(),
        ];
        if (!Feature::isActive('v6.6.0.0')) {
            $customerUpdate['active'] = true;
        }
        $this->customerRepository->update([$customerUpdate], $context->getContext());

        $newToken = $this->contextPersister->replace($context->getToken(), $context);

        $this->contextPersister->save(
            $newToken,
            [
                'customerId' => $customer->getId(),
                'billingAddressId' => null,
                'shippingAddressId' => null,
            ],
            $context->getSalesChannel()->getId(),
            $customer->getId()
        );

        $new = $this->contextService->get(
            new SalesChannelContextServiceParameters(
                $context->getSalesChannel()->getId(),
                $newToken,
                $context->getLanguageId(),
                $context->getCurrencyId(),
                $context->getDomainId(),
                $context->getContext(),
                $customer->getId()
            )
        );

        $new->addState(...$context->getStates());

        if ($customer->getGuest()) {
            $this->eventDispatcher->dispatch(new GuestCustomerRegisterEvent($new, $customer));
        } else {
            $this->eventDispatcher->dispatch(new CustomerRegisterEvent($new, $customer));
        }

        $criteria = new Criteria([$customer->getId()]);
        $criteria->addAssociation('addresses');
        $criteria->addAssociation('salutation');
        $criteria->setLimit(1);

        $customer = $this->customerRepository
            ->search($criteria, $new->getContext())
            ->first();

        \assert($customer instanceof CustomerEntity);

        $response = new CustomerResponse($customer);

        $event = new CustomerLoginEvent($new, $customer, $newToken);
        $this->eventDispatcher->dispatch($event);

        $response->headers->set(PlatformRequest::HEADER_CONTEXT_TOKEN, $newToken);

        return $response;
    }

    private function getBeforeConfirmValidation(string $emHash): DataValidationDefinition
    {
        $definition = new DataValidationDefinition('registration.opt_in_before');
        $definition->add('em', new EqualTo(['value' => $emHash]));
        $definition->add('doubleOptInRegistration', new IsTrue());

        return $definition;
    }
}
