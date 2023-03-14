<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\SalesChannel;

use Laser\Core\Checkout\Cart\AbstractCartPersister;
use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Checkout\Cart\CartCalculator;
use Laser\Core\Checkout\Cart\Event\CheckoutOrderPlacedCriteriaEvent;
use Laser\Core\Checkout\Cart\Event\CheckoutOrderPlacedEvent;
use Laser\Core\Checkout\Cart\Order\OrderPersisterInterface;
use Laser\Core\Checkout\Cart\TaxProvider\TaxProviderProcessor;
use Laser\Core\Checkout\Order\OrderEntity;
use Laser\Core\Checkout\Order\SalesChannel\OrderService;
use Laser\Core\Checkout\Payment\Exception\InvalidOrderException;
use Laser\Core\Checkout\Payment\PreparedPaymentService;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Plugin\Exception\DecorationPatternException;
use Laser\Core\Framework\Validation\DataBag\DataBag;
use Laser\Core\Framework\Validation\DataBag\RequestDataBag;
use Laser\Core\Profiling\Profiler;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Route(defaults: ['_routeScope' => ['store-api']])]
#[Package('checkout')]
class CartOrderRoute extends AbstractCartOrderRoute
{
    /**
     * @internal
     */
    public function __construct(
        private readonly CartCalculator $cartCalculator,
        private readonly EntityRepository $orderRepository,
        private readonly OrderPersisterInterface $orderPersister,
        private readonly AbstractCartPersister $cartPersister,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly PreparedPaymentService $preparedPaymentService,
        private readonly TaxProviderProcessor $taxProviderProcessor
    ) {
    }

    public function getDecorated(): AbstractCartOrderRoute
    {
        throw new DecorationPatternException(self::class);
    }

    #[Route(path: '/store-api/checkout/order', name: 'store-api.checkout.cart.order', methods: ['POST'], defaults: ['_loginRequired' => true, '_loginRequiredAllowGuest' => true])]
    public function order(Cart $cart, SalesChannelContext $context, RequestDataBag $data): CartOrderRouteResponse
    {
        // we use this state in stock updater class, to prevent duplicate available stock updates
        $context->addState('checkout-order-route');

        $calculatedCart = $this->cartCalculator->calculate($cart, $context);
        $this->taxProviderProcessor->process($calculatedCart, $context);

        $this->addCustomerComment($calculatedCart, $data);
        $this->addAffiliateTracking($calculatedCart, $data);

        $preOrderPayment = Profiler::trace('checkout-order::pre-payment', fn () => $this->preparedPaymentService->handlePreOrderPayment($calculatedCart, $data, $context));

        $orderId = Profiler::trace('checkout-order::order-persist', fn () => $this->orderPersister->persist($calculatedCart, $context));

        $criteria = new Criteria([$orderId]);
        $criteria->setTitle('order-route::order-loading');
        $criteria
            ->addAssociation('orderCustomer.customer')
            ->addAssociation('orderCustomer.salutation')
            ->addAssociation('deliveries.shippingMethod')
            ->addAssociation('deliveries.shippingOrderAddress.country')
            ->addAssociation('deliveries.shippingOrderAddress.countryState')
            ->addAssociation('transactions.paymentMethod')
            ->addAssociation('lineItems.cover')
            ->addAssociation('lineItems.downloads.media')
            ->addAssociation('currency')
            ->addAssociation('addresses.country')
            ->addAssociation('addresses.countryState')
            ->getAssociation('transactions')->addSorting(new FieldSorting('createdAt'));

        $this->eventDispatcher->dispatch(new CheckoutOrderPlacedCriteriaEvent($criteria, $context));

        /** @var OrderEntity|null $orderEntity */
        $orderEntity = Profiler::trace('checkout-order::order-loading', fn () => $this->orderRepository->search($criteria, $context->getContext())->first());

        if (!$orderEntity) {
            throw new InvalidOrderException($orderId);
        }

        $event = new CheckoutOrderPlacedEvent(
            $context->getContext(),
            $orderEntity,
            $context->getSalesChannel()->getId()
        );

        Profiler::trace('checkout-order::event-listeners', function () use ($event): void {
            $this->eventDispatcher->dispatch($event);
        });

        $this->cartPersister->delete($context->getToken(), $context);

        Profiler::trace('checkout-order::post-payment', function () use ($orderEntity, $data, $context, $preOrderPayment): void {
            $this->preparedPaymentService->handlePostOrderPayment($orderEntity, $data, $context, $preOrderPayment);
        });

        return new CartOrderRouteResponse($orderEntity);
    }

    private function addCustomerComment(Cart $cart, DataBag $data): void
    {
        $customerComment = ltrim(rtrim((string) $data->get(OrderService::CUSTOMER_COMMENT_KEY, '')));

        if ($customerComment === '') {
            return;
        }

        $cart->setCustomerComment($customerComment);
    }

    private function addAffiliateTracking(Cart $cart, DataBag $data): void
    {
        $affiliateCode = $data->get(OrderService::AFFILIATE_CODE_KEY);
        $campaignCode = $data->get(OrderService::CAMPAIGN_CODE_KEY);
        if ($affiliateCode) {
            $cart->setAffiliateCode($affiliateCode);
        }

        if ($campaignCode) {
            $cart->setCampaignCode($campaignCode);
        }
    }
}
