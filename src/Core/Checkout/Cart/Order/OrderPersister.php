<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\Order;

use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Checkout\Cart\CartException;
use Laser\Core\Checkout\Cart\Exception\CustomerNotLoggedInException;
use Laser\Core\Checkout\Cart\Exception\InvalidCartException;
use Laser\Core\Checkout\Order\Exception\DeliveryWithoutAddressException;
use Laser\Core\Checkout\Order\Exception\EmptyCartException;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Exception\InconsistentCriteriaIdsException;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('checkout')]
class OrderPersister implements OrderPersisterInterface
{
    /**
     * @internal
     */
    public function __construct(
        private readonly EntityRepository $orderRepository,
        private readonly OrderConverter $converter
    ) {
    }

    /**
     * @throws CustomerNotLoggedInException
     * @throws DeliveryWithoutAddressException
     * @throws EmptyCartException
     * @throws InvalidCartException
     * @throws InconsistentCriteriaIdsException
     */
    public function persist(Cart $cart, SalesChannelContext $context): string
    {
        if ($cart->getErrors()->blockOrder()) {
            throw CartException::invalidCart($cart->getErrors());
        }

        if (!$context->getCustomer()) {
            throw CartException::customerNotLoggedIn();
        }
        if ($cart->getLineItems()->count() <= 0) {
            throw new EmptyCartException();
        }

        $order = $this->converter->convertToOrder($cart, $context, new OrderConversionContext());

        $context->getContext()->scope(Context::SYSTEM_SCOPE, function (Context $context) use ($order): void {
            $this->orderRepository->create([$order], $context);
        });

        return $order['id'];
    }
}
