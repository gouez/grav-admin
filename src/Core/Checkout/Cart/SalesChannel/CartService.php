<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\SalesChannel;

use Laser\Core\Checkout\Cart\AbstractCartPersister;
use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Checkout\Cart\CartCalculator;
use Laser\Core\Checkout\Cart\CartException;
use Laser\Core\Checkout\Cart\Event\CartChangedEvent;
use Laser\Core\Checkout\Cart\Event\CartCreatedEvent;
use Laser\Core\Checkout\Cart\LineItem\LineItem;
use Laser\Core\Checkout\Payment\Exception\InvalidOrderException;
use Laser\Core\Framework\DataAbstractionLayer\Exception\InconsistentCriteriaIdsException;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Validation\DataBag\RequestDataBag;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Service\ResetInterface;

#[Package('checkout')]
class CartService implements ResetInterface
{
    /**
     * @var Cart[]
     */
    private array $cart = [];

    /**
     * @internal
     */
    public function __construct(
        private readonly AbstractCartPersister $persister,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly CartCalculator $calculator,
        private readonly AbstractCartLoadRoute $loadRoute,
        private readonly AbstractCartDeleteRoute $deleteRoute,
        private readonly AbstractCartItemAddRoute $itemAddRoute,
        private readonly AbstractCartItemUpdateRoute $itemUpdateRoute,
        private readonly AbstractCartItemRemoveRoute $itemRemoveRoute,
        private readonly AbstractCartOrderRoute $orderRoute
    ) {
    }

    public function setCart(Cart $cart): void
    {
        $this->cart[$cart->getToken()] = $cart;
    }

    public function createNew(string $token): Cart
    {
        $cart = new Cart($token);

        $this->eventDispatcher->dispatch(new CartCreatedEvent($cart));

        return $this->cart[$cart->getToken()] = $cart;
    }

    public function getCart(
        string $token,
        SalesChannelContext $context,
        bool $caching = true,
        bool $taxed = false
    ): Cart {
        if ($caching && isset($this->cart[$token])) {
            return $this->cart[$token];
        }

        $request = new Request();
        $request->query->set('token', $token);
        $request->query->set('taxed', $taxed);

        $cart = $this->loadRoute->load($request, $context)->getCart();

        return $this->cart[$cart->getToken()] = $cart;
    }

    /**
     * @param LineItem|LineItem[] $items
     *
     * @throws CartException
     */
    public function add(Cart $cart, LineItem|array $items, SalesChannelContext $context): Cart
    {
        if ($items instanceof LineItem) {
            $items = [$items];
        }

        $cart = $this->itemAddRoute->add(new Request(), $cart, $context, $items)->getCart();

        return $this->cart[$cart->getToken()] = $cart;
    }

    /**
     * @throws CartException
     */
    public function changeQuantity(Cart $cart, string $identifier, int $quantity, SalesChannelContext $context): Cart
    {
        $request = new Request();
        $request->request->set('items', [
            [
                'id' => $identifier,
                'quantity' => $quantity,
            ],
        ]);

        $cart = $this->itemUpdateRoute->change($request, $cart, $context)->getCart();

        return $this->cart[$cart->getToken()] = $cart;
    }

    /**
     * @throws CartException
     */
    public function remove(Cart $cart, string $identifier, SalesChannelContext $context): Cart
    {
        $request = new Request();
        $request->request->set('ids', [$identifier]);

        $cart = $this->itemRemoveRoute->remove($request, $cart, $context)->getCart();

        return $this->cart[$cart->getToken()] = $cart;
    }

    /**
     * @throws InvalidOrderException
     * @throws InconsistentCriteriaIdsException
     */
    public function order(Cart $cart, SalesChannelContext $context, RequestDataBag $data): string
    {
        $orderId = $this->orderRoute->order($cart, $context, $data)->getOrder()->getId();

        if (isset($this->cart[$cart->getToken()])) {
            unset($this->cart[$cart->getToken()]);
        }

        $cart = $this->createNew($context->getToken());
        $this->eventDispatcher->dispatch(new CartChangedEvent($cart, $context));

        return $orderId;
    }

    public function recalculate(Cart $cart, SalesChannelContext $context): Cart
    {
        $cart = $this->calculator->calculate($cart, $context);
        $this->persister->save($cart, $context);

        return $cart;
    }

    public function deleteCart(SalesChannelContext $context): void
    {
        $this->deleteRoute->delete($context);
    }

    public function reset(): void
    {
        $this->cart = [];
    }
}
