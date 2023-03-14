<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Promotion\Cart;

use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Checkout\Cart\CartBehavior;
use Laser\Core\Checkout\Cart\LineItem\LineItem;
use Laser\Core\Checkout\Cart\LineItem\LineItemCollection;
use Laser\Core\Checkout\Cart\Price\Struct\AbsolutePriceDefinition;
use Laser\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Laser\Core\Checkout\Cart\Price\Struct\CartPrice;
use Laser\Core\Checkout\Cart\Tax\Struct\CalculatedTaxCollection;
use Laser\Core\Checkout\Cart\Tax\Struct\TaxRuleCollection;
use Laser\Core\Checkout\Promotion\Aggregate\PromotionDiscount\PromotionDiscountEntity;
use Laser\Core\Checkout\Promotion\Cart\Error\PromotionExcludedError;
use Laser\Core\Checkout\Promotion\Cart\PromotionCalculator;
use Laser\Core\Checkout\Promotion\Cart\PromotionProcessor;
use Laser\Core\Checkout\Promotion\PromotionDefinition;
use Laser\Core\Checkout\Test\Cart\LineItem\Group\Helpers\Traits\LineItemTestFixtureBehaviour;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextService;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextServiceParameters;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Laser\Core\Test\TestDefaults;

/**
 * @internal
 */
#[Package('checkout')]
class PromotionCalculatorTest extends TestCase
{
    use IntegrationTestBehaviour;
    use LineItemTestFixtureBehaviour;

    private PromotionCalculator $promotionCalculator;

    private SalesChannelContext $salesChannelContext;

    private EntityRepository $promotionRepository;

    protected function setUp(): void
    {
        $container = $this->getContainer();
        $this->promotionCalculator = $container->get(PromotionCalculator::class);
        $this->promotionRepository = $container->get(\sprintf('%s.repository', PromotionDefinition::ENTITY_NAME));

        $salesChannelService = $container->get(SalesChannelContextService::class);
        $this->salesChannelContext = $salesChannelService->get(
            new SalesChannelContextServiceParameters(
                TestDefaults::SALES_CHANNEL,
                Uuid::randomHex()
            )
        );
    }

    public function testCalculateDoesNotAddDiscountItemsWithoutScope(): void
    {
        $discountItem = new LineItem(Uuid::randomHex(), PromotionProcessor::LINE_ITEM_TYPE);
        $discountItems = new LineItemCollection([$discountItem]);
        $original = new Cart(Uuid::randomHex());
        $toCalculate = new Cart(Uuid::randomHex());

        $this->promotionCalculator->calculate($discountItems, $original, $toCalculate, $this->salesChannelContext, new CartBehavior());
        static::assertEmpty($toCalculate->getLineItems());
    }

    public function testCalculateDoesNotAddDiscountItemsWithDeliveryScope(): void
    {
        $discountItem = new LineItem(Uuid::randomHex(), PromotionProcessor::LINE_ITEM_TYPE);
        $discountItem->setPayloadValue('discountScope', PromotionDiscountEntity::SCOPE_DELIVERY);
        $discountItem->setPayloadValue('exclusions', []);
        $discountItems = new LineItemCollection([$discountItem]);
        $original = new Cart(Uuid::randomHex());
        $toCalculate = new Cart(Uuid::randomHex());

        $this->promotionCalculator->calculate($discountItems, $original, $toCalculate, $this->salesChannelContext, new CartBehavior());
        static::assertEmpty($toCalculate->getLineItems());
    }

    public function testCalculateAddsValidPromotionToCalculatedCart(): void
    {
        $promotionId = $this->getPromotionId();
        $discountItem = $this->getDiscountItem($promotionId);

        $discountItems = new LineItemCollection([$discountItem]);
        $original = new Cart(Uuid::randomHex());

        $productLineItem = new LineItem(Uuid::randomHex(), LineItem::PRODUCT_LINE_ITEM_TYPE);
        $productLineItem->setPrice(new CalculatedPrice(100.0, 100.0, new CalculatedTaxCollection(), new TaxRuleCollection()));
        $productLineItem->setStackable(true);

        $toCalculate = new Cart(Uuid::randomHex());
        $toCalculate->add($productLineItem);
        $toCalculate->setPrice(new CartPrice(84.03, 100.0, 100.0, new CalculatedTaxCollection(), new TaxRuleCollection(), CartPrice::TAX_STATE_GROSS));

        $this->promotionCalculator->calculate($discountItems, $original, $toCalculate, $this->salesChannelContext, new CartBehavior());
        static::assertCount(2, $toCalculate->getLineItems());
        $promotionLineItems = $toCalculate->getLineItems()->filterType(PromotionProcessor::LINE_ITEM_TYPE);
        static::assertCount(1, $promotionLineItems);
        static::assertSame(-10.0, $promotionLineItems->first()->getPrice()->getTotalPrice());
    }

    public function testCalculateWithPreventedCombination(): void
    {
        $nonePreventedPromotionId = $this->getPromotionId();
        $discountItemToBeExcluded = $this->getDiscountItem($nonePreventedPromotionId);

        $preventedPromotionId = $this->getPromotionId(true);
        $validDiscountItem = $this->getDiscountItem($preventedPromotionId);
        $validDiscountItem->setPayloadValue('preventCombination', true);

        $discountItems = new LineItemCollection([$discountItemToBeExcluded, $validDiscountItem]);

        $original = new Cart(Uuid::randomHex());

        $productLineItem = new LineItem(Uuid::randomHex(), LineItem::PRODUCT_LINE_ITEM_TYPE);
        $productLineItem->setPrice(new CalculatedPrice(100.0, 100.0, new CalculatedTaxCollection(), new TaxRuleCollection()));
        $productLineItem->setStackable(true);

        $toCalculate = new Cart(Uuid::randomHex());
        $toCalculate->add($productLineItem);
        $toCalculate->setPrice(new CartPrice(84.03, 100.0, 100.0, new CalculatedTaxCollection(), new TaxRuleCollection(), CartPrice::TAX_STATE_GROSS));

        // We expect the product plus 1 promotion in the cart so overall a count of 2 items
        $this->promotionCalculator->calculate($discountItems, $original, $toCalculate, $this->salesChannelContext, new CartBehavior());
        static::assertCount(2, $toCalculate->getLineItems());

        // Make sure that only the expected promotion is in the cart
        $promotionLineItems = $toCalculate->getLineItems()->filterType(PromotionProcessor::LINE_ITEM_TYPE);
        static::assertCount(1, $promotionLineItems);

        $promotionItem = $promotionLineItems->first();
        static::assertSame(-10.0, $promotionItem->getPrice()->getTotalPrice());
        static::assertSame($promotionItem->getReferencedId(), $validDiscountItem->getReferencedId());
    }

    public function testAutomaticExclusionsDontAddError(): void
    {
        $firstPromotionId = $this->getPromotionId(true, 1, false);
        $discountItemToBeExcluded = $this->getDiscountItem($firstPromotionId);
        $discountItemToBeExcluded->setPayloadValue('preventCombination', true);

        $secondPromotionId = $this->getPromotionId(true, 2, false);
        $validDiscountItem = $this->getDiscountItem($secondPromotionId);
        $validDiscountItem->setPayloadValue('preventCombination', true);

        $discountItems = new LineItemCollection([$validDiscountItem, $discountItemToBeExcluded]);

        $original = new Cart(Uuid::randomHex());

        $productLineItem = new LineItem(Uuid::randomHex(), LineItem::PRODUCT_LINE_ITEM_TYPE);
        $productLineItem->setPrice(new CalculatedPrice(100.0, 100.0, new CalculatedTaxCollection(), new TaxRuleCollection()));
        $productLineItem->setStackable(true);

        $toCalculate = new Cart(Uuid::randomHex());
        $toCalculate->add($productLineItem);
        $toCalculate->setPrice(new CartPrice(84.03, 100.0, 100.0, new CalculatedTaxCollection(), new TaxRuleCollection(), CartPrice::TAX_STATE_GROSS));

        // We expect the product plus 1 promotion in the cart so overall a count of 2 items
        $this->promotionCalculator->calculate($discountItems, $original, $toCalculate, $this->salesChannelContext, new CartBehavior());
        static::assertCount(2, $toCalculate->getLineItems());

        // Make sure that only the expected promotion is in the cart
        $promotionLineItems = $toCalculate->getLineItems()->filterType(PromotionProcessor::LINE_ITEM_TYPE);
        static::assertCount(1, $promotionLineItems);

        $promotionItem = $promotionLineItems->first();
        static::assertSame(-10.0, $promotionItem->getPrice()->getTotalPrice());
        static::assertSame($promotionItem->getReferencedId(), $validDiscountItem->getReferencedId());

        $cartErrors = $toCalculate->getErrors();
        foreach ($cartErrors as $cartError) {
            static::assertNotInstanceOf(PromotionExcludedError::class, $cartError);
        }
    }

    private function getPromotionId(bool $preventCombination = false, int $priority = 1, bool $useCodes = true): string
    {
        $promotionId = Uuid::randomHex();

        $promotionData = [
            'id' => $promotionId,
            'active' => true,
            'exclusive' => false,
            'priority' => $priority,
            'code' => \sprintf('phpUnit-%s', $promotionId),
            'useCodes' => $useCodes,
            'useIndividualCodes' => false,
            'useSetGroups' => false,
            'name' => 'PHP Unit promotion',
            'preventCombination' => $preventCombination,
            'discounts' => [
                [
                    'scope' => PromotionDiscountEntity::SCOPE_CART,
                    'type' => PromotionDiscountEntity::TYPE_ABSOLUTE,
                    'value' => 10.0,
                    'considerAdvancedRules' => false,
                ],
            ],
        ];

        if (!$useCodes) {
            unset($promotionData['code']);
        }

        $this->promotionRepository->create(
            [
                $promotionData,
            ],
            $this->salesChannelContext->getContext()
        );

        return $promotionId;
    }

    private function getDiscountItem(string $promotionId): LineItem
    {
        $discountItemToBeExcluded = new LineItem(Uuid::randomHex(), PromotionProcessor::LINE_ITEM_TYPE);
        $discountItemToBeExcluded->setRequirement(null);
        $discountItemToBeExcluded->setPayloadValue('discountScope', PromotionDiscountEntity::SCOPE_CART);
        $discountItemToBeExcluded->setPayloadValue('discountType', PromotionDiscountEntity::TYPE_ABSOLUTE);
        $discountItemToBeExcluded->setPayloadValue('exclusions', []);
        $discountItemToBeExcluded->setPayloadValue('promotionId', $promotionId);
        $discountItemToBeExcluded->setReferencedId($promotionId);
        $discountItemToBeExcluded->setLabel('PHPUnit');
        $discountItemToBeExcluded->setPriceDefinition(new AbsolutePriceDefinition(-10.0));

        return $discountItemToBeExcluded;
    }
}
