<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Customer\Subscriber;

use Laser\Core\Checkout\Customer\CustomerEvents;
use Laser\Core\Checkout\Customer\DataAbstractionLayer\CustomerIndexer;
use Laser\Core\Checkout\Customer\DataAbstractionLayer\CustomerIndexingMessage;
use Laser\Core\Checkout\Customer\Event\CustomerChangedPaymentMethodEvent;
use Laser\Core\Checkout\Customer\Event\CustomerRegisterEvent;
use Laser\Core\Framework\Api\Context\SalesChannelApiSource;
use Laser\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Validation\DataBag\RequestDataBag;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextRestorer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 */
#[Package('business-ops')]
class CustomerFlowEventsSubscriber implements EventSubscriberInterface
{
    /**
     * @internal
     */
    public function __construct(
        private readonly EventDispatcherInterface $dispatcher,
        private readonly SalesChannelContextRestorer $restorer,
        private readonly CustomerIndexer $customerIndexer
    ) {
    }

    /**
     * @return array<string, string|array{0: string, 1: int}|list<array{0: string, 1?: int}>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            CustomerEvents::CUSTOMER_WRITTEN_EVENT => 'onCustomerWritten',
        ];
    }

    public function onCustomerWritten(EntityWrittenEvent $event): void
    {
        if ($event->getContext()->getSource() instanceof SalesChannelApiSource) {
            return;
        }

        $payloads = $event->getPayloads();

        foreach ($payloads as $payload) {
            if (!empty($payload['defaultPaymentMethodId']) && empty($payload['createdAt'])) {
                $this->dispatchCustomerChangePaymentMethodEvent($payload['id'], $event);

                continue;
            }

            if (!empty($payload['createdAt'])) {
                $this->dispatchCustomerRegisterEvent($payload['id'], $event);
            }
        }
    }

    private function dispatchCustomerRegisterEvent(string $customerId, EntityWrittenEvent $event): void
    {
        $context = $event->getContext();
        $message = new CustomerIndexingMessage([$customerId]);
        $this->customerIndexer->handle($message);

        $salesChannelContext = $this->restorer->restoreByCustomer($customerId, $context);

        if (!$customer = $salesChannelContext->getCustomer()) {
            return;
        }

        $customerCreated = new CustomerRegisterEvent(
            $salesChannelContext,
            $customer
        );

        $this->dispatcher->dispatch($customerCreated);
    }

    private function dispatchCustomerChangePaymentMethodEvent(string $customerId, EntityWrittenEvent $event): void
    {
        $context = $event->getContext();
        $salesChannelContext = $this->restorer->restoreByCustomer($customerId, $context);

        if (!$customer = $salesChannelContext->getCustomer()) {
            return;
        }

        $customerChangePaymentMethodEvent = new CustomerChangedPaymentMethodEvent(
            $salesChannelContext,
            $customer,
            new RequestDataBag()
        );

        $this->dispatcher->dispatch($customerChangePaymentMethodEvent);
    }
}
