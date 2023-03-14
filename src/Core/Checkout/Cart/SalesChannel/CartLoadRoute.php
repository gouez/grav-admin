<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\SalesChannel;

use Laser\Core\Checkout\Cart\AbstractCartPersister;
use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Checkout\Cart\CartCalculator;
use Laser\Core\Checkout\Cart\Event\CartCreatedEvent;
use Laser\Core\Checkout\Cart\Exception\CartTokenNotFoundException;
use Laser\Core\Checkout\Cart\TaxProvider\TaxProviderProcessor;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Plugin\Exception\DecorationPatternException;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Route(defaults: ['_routeScope' => ['store-api']])]
#[Package('checkout')]
class CartLoadRoute extends AbstractCartLoadRoute
{
    /**
     * @internal
     */
    public function __construct(
        private readonly AbstractCartPersister $persister,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly CartCalculator $cartCalculator,
        private readonly TaxProviderProcessor $taxProviderProcessor
    ) {
    }

    public function getDecorated(): AbstractCartLoadRoute
    {
        throw new DecorationPatternException(self::class);
    }

    #[Route(path: '/store-api/checkout/cart', name: 'store-api.checkout.cart.read', methods: ['GET', 'POST'])]
    public function load(Request $request, SalesChannelContext $context): CartResponse
    {
        $token = $request->get('token', $context->getToken());
        $taxed = $request->get('taxed', false);

        try {
            $cart = $this->persister->load($token, $context);
        } catch (CartTokenNotFoundException) {
            $cart = $this->createNew($token);
        }

        $cart = $this->cartCalculator->calculate($cart, $context);

        if ($taxed) {
            $this->taxProviderProcessor->process($cart, $context);
        }

        return new CartResponse($cart);
    }

    private function createNew(string $token): Cart
    {
        $cart = new Cart($token);

        $this->eventDispatcher->dispatch(new CartCreatedEvent($cart));

        return $cart;
    }
}
