<?php
declare(strict_types=1);

namespace Laser\Core\Checkout\Promotion\Subscriber;

use Laser\Core\Checkout\Cart\Event\CheckoutOrderPlacedEvent;
use Laser\Core\Checkout\Order\Aggregate\OrderCustomer\OrderCustomerEntity;
use Laser\Core\Checkout\Promotion\Aggregate\PromotionIndividualCode\PromotionIndividualCodeCollection;
use Laser\Core\Checkout\Promotion\Aggregate\PromotionIndividualCode\PromotionIndividualCodeEntity;
use Laser\Core\Checkout\Promotion\Cart\PromotionProcessor;
use Laser\Core\Checkout\Promotion\Exception\CodeAlreadyRedeemedException;
use Laser\Core\Checkout\Promotion\Exception\PromotionCodeNotFoundException;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Exception\InconsistentCriteriaIdsException;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Laser\Core\Framework\Log\Package;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @internal
 */
#[Package('checkout')]
class PromotionIndividualCodeRedeemer implements EventSubscriberInterface
{
    /**
     * @internal
     */
    public function __construct(private readonly EntityRepository $codesRepository)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CheckoutOrderPlacedEvent::class => 'onOrderPlaced',
        ];
    }

    /**
     * @throws CodeAlreadyRedeemedException
     * @throws InconsistentCriteriaIdsException
     */
    public function onOrderPlaced(CheckoutOrderPlacedEvent $event): void
    {
        foreach ($event->getOrder()->getLineItems() ?? [] as $item) {
            // only update promotions in here
            if ($item->getType() !== PromotionProcessor::LINE_ITEM_TYPE) {
                continue;
            }

            /** @var string $code */
            $code = $item->getPayload()['code'] ?? '';

            try {
                // first try if its an individual
                // if not, then it might be a global promotion
                $individualCode = $this->getIndividualCode($code, $event->getContext());
            } catch (PromotionCodeNotFoundException) {
                $individualCode = null;
            }

            // if we did not use an individual code we might have
            // just used a global one or anything else, so just quit in this case.
            if (!($individualCode instanceof PromotionIndividualCodeEntity)) {
                return;
            }

            /** @var OrderCustomerEntity $customer */
            $customer = $event->getOrder()->getOrderCustomer();

            // set the code to be redeemed
            // and assign all required meta data
            // for later needs
            $individualCode->setRedeemed(
                $item->getOrderId(),
                $customer->getCustomerId() ?? '',
                $customer->getFirstName() . ' ' . $customer->getLastName()
            );

            // save in database
            $this->codesRepository->update(
                [
                    [
                        'id' => $individualCode->getId(),
                        'payload' => $individualCode->getPayload(),
                    ],
                ],
                $event->getContext()
            );
        }
    }

    /**
     * Gets all individual code entities for the provided code value.
     *
     * @throws PromotionCodeNotFoundException
     * @throws InconsistentCriteriaIdsException
     */
    private function getIndividualCode(string $code, Context $context): PromotionIndividualCodeEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsFilter('code', $code)
        );

        /** @var PromotionIndividualCodeCollection $result */
        $result = $this->codesRepository->search($criteria, $context)->getEntities();

        if (\count($result->getElements()) <= 0) {
            throw new PromotionCodeNotFoundException($code);
        }

        // return first element
        return $result->first();
    }
}
