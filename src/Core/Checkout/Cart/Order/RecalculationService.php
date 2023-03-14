<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\Order;

use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Checkout\Cart\CartBehavior;
use Laser\Core\Checkout\Cart\CartException;
use Laser\Core\Checkout\Cart\CartRuleLoader;
use Laser\Core\Checkout\Cart\Delivery\Struct\DeliveryPosition;
use Laser\Core\Checkout\Cart\Exception\CustomerNotLoggedInException;
use Laser\Core\Checkout\Cart\LineItem\LineItem;
use Laser\Core\Checkout\Cart\Order\Transformer\AddressTransformer;
use Laser\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Laser\Core\Checkout\Cart\Processor;
use Laser\Core\Checkout\Cart\SalesChannel\CartService;
use Laser\Core\Checkout\Customer\Aggregate\CustomerAddress\CustomerAddressEntity;
use Laser\Core\Checkout\Customer\Exception\AddressNotFoundException;
use Laser\Core\Checkout\Order\Exception\DeliveryWithoutAddressException;
use Laser\Core\Checkout\Order\Exception\EmptyCartException;
use Laser\Core\Checkout\Order\OrderEntity;
use Laser\Core\Checkout\Order\OrderException;
use Laser\Core\Checkout\Payment\Exception\InvalidOrderException;
use Laser\Core\Checkout\Promotion\Cart\PromotionCollector;
use Laser\Core\Checkout\Promotion\Cart\PromotionItemBuilder;
use Laser\Core\Content\Product\Exception\ProductNotFoundException;
use Laser\Core\Defaults;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\Entity;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Exception\InconsistentCriteriaIdsException;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextService;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('checkout')]
class RecalculationService
{
    /**
     * @internal
     */
    public function __construct(
        protected EntityRepository $orderRepository,
        protected OrderConverter $orderConverter,
        protected CartService $cartService,
        protected EntityRepository $productRepository,
        protected EntityRepository $orderAddressRepository,
        protected EntityRepository $customerAddressRepository,
        protected Processor $processor,
        private readonly CartRuleLoader $cartRuleLoader,
        private readonly PromotionItemBuilder $promotionItemBuilder
    ) {
    }

    /**
     * @throws InvalidOrderException
     * @throws CustomerNotLoggedInException
     * @throws CartException
     * @throws DeliveryWithoutAddressException
     * @throws EmptyCartException
     * @throws InconsistentCriteriaIdsException
     */
    public function recalculateOrder(string $orderId, Context $context): void
    {
        $order = $this->fetchOrder($orderId, $context);

        $this->validateOrder($order, $orderId);
        \assert($order instanceof OrderEntity);

        $salesChannelContext = $this->orderConverter->assembleSalesChannelContext($order, $context);
        $cart = $this->orderConverter->convertToCart($order, $context);
        $recalculatedCart = $this->recalculateCart($cart, $salesChannelContext);

        $conversionContext = (new OrderConversionContext())
            ->setIncludeCustomer(false)
            ->setIncludeBillingAddress(false)
            ->setIncludeDeliveries(true)
            ->setIncludeTransactions(false)
            ->setIncludeOrderDate(false);

        $orderData = $this->orderConverter->convertToOrder($recalculatedCart, $salesChannelContext, $conversionContext);
        $orderData['id'] = $order->getId();

        // change scope to be able to write protected state fields of transactions and deliveries
        $context->scope(Context::SYSTEM_SCOPE, function (Context $context) use ($orderData): void {
            $this->orderRepository->upsert([$orderData], $context);
        });
    }

    /**
     * @throws DeliveryWithoutAddressException
     * @throws InconsistentCriteriaIdsException
     * @throws InvalidOrderException
     * @throws CartException
     * @throws ProductNotFoundException
     */
    public function addProductToOrder(string $orderId, string $productId, int $quantity, Context $context): void
    {
        $this->validateProduct($productId, $context);
        $lineItem = (new LineItem($productId, LineItem::PRODUCT_LINE_ITEM_TYPE, $productId, $quantity))
            ->setRemovable(true)
            ->setStackable(true);

        $order = $this->fetchOrder($orderId, $context);

        $this->validateOrder($order, $orderId);

        \assert($order instanceof OrderEntity);

        $salesChannelContext = $this->orderConverter->assembleSalesChannelContext($order, $context);
        $cart = $this->orderConverter->convertToCart($order, $context);
        $cart->add($lineItem);

        $recalculatedCart = $this->recalculateCart($cart, $salesChannelContext);

        $new = $cart->get($lineItem->getId());
        if ($new) {
            $this->addProductToDeliveryPosition($new, $recalculatedCart);
        }

        $conversionContext = (new OrderConversionContext())
            ->setIncludeCustomer(false)
            ->setIncludeBillingAddress(false)
            ->setIncludeDeliveries(true)
            ->setIncludeTransactions(false)
            ->setIncludeOrderDate(false);

        $orderData = $this->orderConverter->convertToOrder($recalculatedCart, $salesChannelContext, $conversionContext);
        $orderData['id'] = $order->getId();

        $context->scope(Context::SYSTEM_SCOPE, function (Context $context) use ($orderData): void {
            $this->orderRepository->upsert([$orderData], $context);
        });
    }

    /**
     * @throws DeliveryWithoutAddressException
     * @throws InconsistentCriteriaIdsException
     * @throws InvalidOrderException
     * @throws CartException
     */
    public function addCustomLineItem(string $orderId, LineItem $lineItem, Context $context): void
    {
        $order = $this->fetchOrder($orderId, $context);

        $this->validateOrder($order, $orderId);
        \assert($order instanceof OrderEntity);

        $salesChannelContext = $this->orderConverter->assembleSalesChannelContext($order, $context);
        $cart = $this->orderConverter->convertToCart($order, $context);
        $cart->add($lineItem);

        $recalculatedCart = $this->recalculateCart($cart, $salesChannelContext);

        $conversionContext = (new OrderConversionContext())
            ->setIncludeCustomer(false)
            ->setIncludeBillingAddress(false)
            ->setIncludeDeliveries(false)
            ->setIncludeTransactions(false)
            ->setIncludeOrderDate(false);

        $orderData = $this->orderConverter->convertToOrder($recalculatedCart, $salesChannelContext, $conversionContext);
        $orderData['id'] = $order->getId();
        $context->scope(Context::SYSTEM_SCOPE, function (Context $context) use ($orderData): void {
            $this->orderRepository->upsert([$orderData], $context);
        });
    }

    public function addPromotionLineItem(string $orderId, string $code, Context $context): Cart
    {
        $order = $this->fetchOrder($orderId, $context);

        $this->validateOrder($order, $orderId);
        \assert($order instanceof OrderEntity);

        $options = [
            SalesChannelContextService::PERMISSIONS => OrderConverter::ADMIN_EDIT_ORDER_PERMISSIONS,
        ];

        $options[SalesChannelContextService::PERMISSIONS] = \array_merge(
            OrderConverter::ADMIN_EDIT_ORDER_PERMISSIONS,
            [
                PromotionCollector::SKIP_PROMOTION => false,
                PromotionCollector::SKIP_AUTOMATIC_PROMOTIONS => true,
            ]
        );

        $salesChannelContext = $this->orderConverter->assembleSalesChannelContext(
            $order,
            $context,
            $options,
        );
        $cart = $this->orderConverter->convertToCart($order, $context);

        $promotionLineItem = $this->promotionItemBuilder->buildPlaceholderItem($code);

        $cart->add($promotionLineItem);
        $recalculatedCart = $this->recalculateCart($cart, $salesChannelContext);

        $conversionContext = (new OrderConversionContext())
            ->setIncludeCustomer(false)
            ->setIncludeBillingAddress(false)
            ->setIncludeDeliveries(false)
            ->setIncludeTransactions(false)
            ->setIncludeOrderDate(false);

        $orderData = $this->orderConverter->convertToOrder($recalculatedCart, $salesChannelContext, $conversionContext);
        $orderData['id'] = $order->getId();

        $context->scope(Context::SYSTEM_SCOPE, function (Context $context) use ($orderData): void {
            $this->orderRepository->upsert([$orderData], $context);
        });

        return $recalculatedCart;
    }

    public function toggleAutomaticPromotion(string $orderId, Context $context, bool $skipAutomaticPromotions = true): Cart
    {
        $order = $this->fetchOrder($orderId, $context);

        $this->validateOrder($order, $orderId);
        \assert($order instanceof OrderEntity);

        $options = [
            SalesChannelContextService::PERMISSIONS => OrderConverter::ADMIN_EDIT_ORDER_PERMISSIONS,
        ];

        $options[SalesChannelContextService::PERMISSIONS] = \array_merge(
            OrderConverter::ADMIN_EDIT_ORDER_PERMISSIONS,
            [
                PromotionCollector::SKIP_PROMOTION => false,
                PromotionCollector::SKIP_AUTOMATIC_PROMOTIONS => $skipAutomaticPromotions,
            ]
        );

        $salesChannelContext = $this->orderConverter->assembleSalesChannelContext(
            $order,
            $context,
            $options,
        );

        $cart = $this->orderConverter->convertToCart($order, $context);

        $recalculatedCart = $this->recalculateCart($cart, $salesChannelContext);

        $conversionContext = (new OrderConversionContext())
            ->setIncludeCustomer(false)
            ->setIncludeBillingAddress(false)
            ->setIncludeDeliveries(false)
            ->setIncludeTransactions(false)
            ->setIncludeOrderDate(false);

        $orderData = $this->orderConverter->convertToOrder($recalculatedCart, $salesChannelContext, $conversionContext);

        $orderData['id'] = $order->getId();
        $context->scope(Context::SYSTEM_SCOPE, function (Context $context) use ($orderData): void {
            $this->orderRepository->upsert([$orderData], $context);
        });

        return $recalculatedCart;
    }

    /**
     * @throws AddressNotFoundException
     * @throws OrderException
     * @throws InconsistentCriteriaIdsException
     */
    public function replaceOrderAddressWithCustomerAddress(string $orderAddressId, string $customerAddressId, Context $context): void
    {
        $this->validateOrderAddress($orderAddressId, $context);

        $criteria = (new Criteria())
            ->addFilter(new EqualsFilter('customer_address.id', $customerAddressId));

        /** @var ?CustomerAddressEntity $customerAddress */
        $customerAddress = $this->customerAddressRepository->search($criteria, $context)->get($customerAddressId);
        if ($customerAddress === null) {
            throw new AddressNotFoundException($customerAddressId);
        }

        $newOrderAddress = AddressTransformer::transform($customerAddress);
        $newOrderAddress['id'] = $orderAddressId;
        $this->orderAddressRepository->upsert([$newOrderAddress], $context);
    }

    private function addProductToDeliveryPosition(LineItem $item, Cart $cart): void
    {
        if ($cart->getDeliveries()->count() <= 0) {
            return;
        }

        $delivery = $cart->getDeliveries()->first();
        if (!$delivery) {
            return;
        }

        $calculatedPrice = $item->getPrice();
        \assert($calculatedPrice instanceof CalculatedPrice);

        $position = new DeliveryPosition($item->getId(), clone $item, $item->getQuantity(), $calculatedPrice, $delivery->getDeliveryDate());

        $delivery->getPositions()->add($position);
    }

    private function fetchOrder(string $orderId, Context $context): ?OrderEntity
    {
        $criteria = (new Criteria([$orderId]))
            ->addAssociation('lineItems.downloads')
            ->addAssociation('transactions')
            ->addAssociation('deliveries.shippingMethod')
            ->addAssociation('deliveries.positions.orderLineItem')
            ->addAssociation('deliveries.shippingOrderAddress.country')
            ->addAssociation('deliveries.shippingOrderAddress.countryState');

        /** @var ?OrderEntity $order */
        $order = $this->orderRepository
            ->search($criteria, $context)
            ->get($orderId);

        return $order;
    }

    /**
     * @throws OrderException
     * @throws InvalidOrderException
     */
    private function validateOrder(?OrderEntity $order, string $orderId): void
    {
        if (!$order) {
            throw new InvalidOrderException($orderId);
        }

        $this->checkVersion($order);
    }

    /**
     * @throws ProductNotFoundException
     * @throws InconsistentCriteriaIdsException
     */
    private function validateProduct(string $productId, Context $context): void
    {
        $product = $this->productRepository->search(new Criteria([$productId]), $context)->get($productId);

        if (!$product) {
            throw new ProductNotFoundException($productId);
        }
    }

    private function checkVersion(Entity $entity): void
    {
        if ($entity->getVersionId() === Defaults::LIVE_VERSION) {
            throw OrderException::canNotRecalculateLiveVersion($entity->getUniqueIdentifier());
        }
    }

    /**
     * @throws AddressNotFoundException
     * @throws OrderException
     * @throws InconsistentCriteriaIdsException
     */
    private function validateOrderAddress(string $orderAddressId, Context $context): void
    {
        $address = $this->orderAddressRepository->search(new Criteria([$orderAddressId]), $context)->get($orderAddressId);
        if (!$address) {
            throw new AddressNotFoundException($orderAddressId);
        }

        $this->checkVersion($address);
    }

    private function recalculateCart(Cart $cart, SalesChannelContext $context): Cart
    {
        $behavior = new CartBehavior($context->getPermissions());

        // all prices are now prepared for calculation -  starts the cart calculation
        $cart = $this->processor->process($cart, $context, $behavior);

        // validate cart against the context rules
        $validated = $this->cartRuleLoader->loadByCart($context, $cart, $behavior);

        $cart = $validated->getCart();

        return $cart;
    }
}
