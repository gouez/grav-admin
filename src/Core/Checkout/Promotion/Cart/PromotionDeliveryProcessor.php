<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Promotion\Cart;

use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Checkout\Cart\CartBehavior;
use Laser\Core\Checkout\Cart\CartProcessorInterface;
use Laser\Core\Checkout\Cart\LineItem\CartDataCollection;
use Laser\Core\Checkout\Cart\LineItem\Group\LineItemGroupBuilder;
use Laser\Core\Checkout\Cart\LineItem\LineItemCollection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('checkout')]
class PromotionDeliveryProcessor implements CartProcessorInterface
{
    final public const SKIP_DELIVERY_RECALCULATION = 'skipDeliveryRecalculation';

    /**
     * @internal
     */
    public function __construct(
        private readonly PromotionDeliveryCalculator $calculator,
        private readonly LineItemGroupBuilder $groupBuilder
    ) {
    }

    public function process(CartDataCollection $data, Cart $original, Cart $toCalculate, SalesChannelContext $context, CartBehavior $behavior): void
    {
        // always make sure we have
        // the line item group builder for our
        // line item group rule inside the cart data
        $toCalculate->getData()->set(LineItemGroupBuilder::class, $this->groupBuilder);

        // if there is no collected promotion we may return - nothing to calculate!
        if (!$data->has(PromotionProcessor::DATA_KEY)) {
            return;
        }

        // if we are in recalculation,
        // we must not re-add any promotions. just leave it as it is.
        if ($behavior->hasPermission(self::SKIP_DELIVERY_RECALCULATION)) {
            return;
        }

        /** @var LineItemCollection $discountLineItems */
        $discountLineItems = $data->get(PromotionProcessor::DATA_KEY);

        // calculate the whole cart with the
        // new list of created promotion discount line items
        $this->calculator->calculate(
            new LineItemCollection($discountLineItems),
            $original,
            $toCalculate,
            $context
        );
    }
}
