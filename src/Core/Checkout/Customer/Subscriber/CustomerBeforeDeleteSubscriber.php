<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Customer\Subscriber;

use Laser\Core\Checkout\Customer\CustomerCollection;
use Laser\Core\Checkout\Customer\CustomerDefinition;
use Laser\Core\Checkout\Customer\Event\CustomerDeletedEvent;
use Laser\Core\Framework\Api\Context\SalesChannelApiSource;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Event\BeforeDeleteEvent;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Util\Random;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextServiceInterface;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextServiceParameters;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @internal
 */
#[Package('customer-order')]
class CustomerBeforeDeleteSubscriber implements EventSubscriberInterface
{
    /**
     * @internal
     */
    public function __construct(
        private readonly EntityRepository $customerRepository,
        private readonly SalesChannelContextServiceInterface $salesChannelContextService,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            BeforeDeleteEvent::class => 'beforeDelete',
        ];
    }

    public function beforeDelete(BeforeDeleteEvent $event): void
    {
        $context = $event->getContext();

        $ids = $event->getIds(CustomerDefinition::ENTITY_NAME);

        if (empty($ids)) {
            return;
        }

        $source = $context->getSource();
        $salesChannelId = null;

        if ($source instanceof SalesChannelApiSource) {
            $salesChannelId = $source->getSalesChannelId();
        }

        /** @var CustomerCollection $customers */
        $customers = $this->customerRepository->search(new Criteria($ids), $context);

        $event->addSuccess(function () use ($customers, $context, $salesChannelId): void {
            foreach ($customers->getElements() as $customer) {
                $salesChannelContext = $this->salesChannelContextService->get(
                    new SalesChannelContextServiceParameters(
                        $salesChannelId ?? $customer->getSalesChannelId(),
                        Random::getAlphanumericString(32),
                        $customer->getLanguageId(),
                        null,
                        null,
                        $context,
                    )
                );

                $this->eventDispatcher->dispatch(new CustomerDeletedEvent($salesChannelContext, $customer));
            }
        });
    }
}
