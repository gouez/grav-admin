<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Promotion\Cart\Discount\Calculator;

use Laser\Core\Checkout\Cart\Price\AbsolutePriceCalculator;
use Laser\Core\Checkout\Cart\Price\Struct\AbsolutePriceDefinition;
use Laser\Core\Checkout\Promotion\Cart\Discount\Composition\DiscountCompositionItem;
use Laser\Core\Checkout\Promotion\Cart\Discount\DiscountCalculatorInterface;
use Laser\Core\Checkout\Promotion\Cart\Discount\DiscountCalculatorResult;
use Laser\Core\Checkout\Promotion\Cart\Discount\DiscountLineItem;
use Laser\Core\Checkout\Promotion\Cart\Discount\DiscountPackageCollection;
use Laser\Core\Checkout\Promotion\Exception\InvalidPriceDefinitionException;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('checkout')]
class DiscountAbsoluteCalculator implements DiscountCalculatorInterface
{
    public function __construct(private readonly AbsolutePriceCalculator $priceCalculator)
    {
    }

    /**
     * @throws InvalidPriceDefinitionException
     */
    public function calculate(DiscountLineItem $discount, DiscountPackageCollection $packages, SalesChannelContext $context): DiscountCalculatorResult
    {
        /** @var AbsolutePriceDefinition $definition */
        $definition = $discount->getPriceDefinition();

        if (!$definition instanceof AbsolutePriceDefinition) {
            throw new InvalidPriceDefinitionException($discount->getLabel(), $discount->getCode());
        }

        $discountValue = -abs($definition->getPrice());

        $price = $this->priceCalculator->calculate(
            $discountValue,
            $packages->getAffectedPrices(),
            $context
        );

        $composition = $this->getCompositionItems(
            $discountValue,
            $packages
        );

        return new DiscountCalculatorResult($price, $composition);
    }

    private function getCompositionItems(float $discountValue, DiscountPackageCollection $packages): array
    {
        $totalOriginalSum = $packages->getAffectedPrices()->sum()->getTotalPrice();

        $items = [];

        foreach ($packages as $package) {
            foreach ($package->getCartItems() as $lineItem) {
                if ($lineItem->getPrice() === null) {
                    continue;
                }

                $itemTotal = $lineItem->getPrice()->getTotalPrice();

                $factor = $itemTotal / $totalOriginalSum;

                $items[] = new DiscountCompositionItem(
                    $lineItem->getId(),
                    $lineItem->getQuantity(),
                    abs($discountValue) * $factor
                );
            }
        }

        return $items;
    }
}
