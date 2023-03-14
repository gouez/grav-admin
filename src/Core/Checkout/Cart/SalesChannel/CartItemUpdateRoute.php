<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\SalesChannel;

use Laser\Core\Checkout\Cart\AbstractCartPersister;
use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Checkout\Cart\CartCalculator;
use Laser\Core\Checkout\Cart\Event\AfterLineItemQuantityChangedEvent;
use Laser\Core\Checkout\Cart\Event\CartChangedEvent;
use Laser\Core\Checkout\Cart\LineItemFactoryRegistry;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Plugin\Exception\DecorationPatternException;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Route(defaults: ['_routeScope' => ['store-api']])]
#[Package('checkout')]
class CartItemUpdateRoute extends AbstractCartItemUpdateRoute
{
    /**
     * @internal
     */
    public function __construct(
        private readonly AbstractCartPersister $cartPersister,
        private readonly CartCalculator $cartCalculator,
        private readonly LineItemFactoryRegistry $lineItemFactory,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function getDecorated(): AbstractCartItemUpdateRoute
    {
        throw new DecorationPatternException(self::class);
    }

    #[Route(path: '/store-api/checkout/cart/line-item', name: 'store-api.checkout.cart.update-lineitem', methods: ['PATCH'])]
    public function change(Request $request, Cart $cart, SalesChannelContext $context): CartResponse
    {
        $itemsToUpdate = $request->request->all('items');

        /** @var array<mixed> $item */
        foreach ($itemsToUpdate as $item) {
            $this->lineItemFactory->update($cart, $item, $context);
        }

        $cart->markModified();

        $cart = $this->cartCalculator->calculate($cart, $context);
        $this->cartPersister->save($cart, $context);

        $this->eventDispatcher->dispatch(new AfterLineItemQuantityChangedEvent($cart, $itemsToUpdate, $context));
        $this->eventDispatcher->dispatch(new CartChangedEvent($cart, $context));

        return new CartResponse($cart);
    }
}
