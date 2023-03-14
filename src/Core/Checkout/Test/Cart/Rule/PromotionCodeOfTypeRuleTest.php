<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Cart\Rule;

use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\Cart\LineItem\LineItem;
use Laser\Core\Checkout\Cart\LineItem\LineItemCollection;
use Laser\Core\Checkout\Cart\Rule\CartRuleScope;
use Laser\Core\Checkout\Promotion\Rule\PromotionCodeOfTypeRule;
use Laser\Core\Checkout\Test\Cart\Rule\Helper\CartRuleHelperTrait;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Write\WriteException;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Rule\Rule;
use Laser\Core\Framework\Test\TestCaseBase\DatabaseTransactionBehaviour;
use Laser\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @internal
 */
#[Package('business-ops')]
class PromotionCodeOfTypeRuleTest extends TestCase
{
    use CartRuleHelperTrait;
    use KernelTestBehaviour;
    use DatabaseTransactionBehaviour;

    private EntityRepository $ruleRepository;

    private EntityRepository $conditionRepository;

    private Context $context;

    private PromotionCodeOfTypeRule $rule;

    protected function setUp(): void
    {
        $this->ruleRepository = $this->getContainer()->get('rule.repository');
        $this->conditionRepository = $this->getContainer()->get('rule_condition.repository');
        $this->context = Context::createDefaultContext();
        $this->rule = new PromotionCodeOfTypeRule();
    }

    public function testValidateWithMissingLineItemType(): void
    {
        try {
            $this->conditionRepository->create([
                [
                    'type' => (new PromotionCodeOfTypeRule())->getName(),
                    'ruleId' => Uuid::randomHex(),
                ],
            ], $this->context);
            static::fail('Exception was not thrown');
        } catch (WriteException $stackException) {
            $exceptions = iterator_to_array($stackException->getErrors());
            static::assertCount(2, $exceptions);
            static::assertSame('/0/value/promotionCodeType', $exceptions[0]['source']['pointer']);
            static::assertSame(NotBlank::IS_BLANK_ERROR, $exceptions[0]['code']);

            static::assertSame('/0/value/operator', $exceptions[1]['source']['pointer']);
            static::assertSame(NotBlank::IS_BLANK_ERROR, $exceptions[1]['code']);
        }
    }

    public function testValidateWithEmptyLineItemType(): void
    {
        try {
            $this->conditionRepository->create([
                [
                    'type' => (new PromotionCodeOfTypeRule())->getName(),
                    'ruleId' => Uuid::randomHex(),
                    'value' => [
                        'promotionCodeType' => '',
                        'operator' => Rule::OPERATOR_EQ,
                    ],
                ],
            ], $this->context);
            static::fail('Exception was not thrown');
        } catch (WriteException $stackException) {
            $exceptions = iterator_to_array($stackException->getErrors());
            static::assertCount(1, $exceptions);
            static::assertSame('/0/value/promotionCodeType', $exceptions[0]['source']['pointer']);
            static::assertSame(NotBlank::IS_BLANK_ERROR, $exceptions[0]['code']);
        }
    }

    public function testValidateWithInvalidLineItemType(): void
    {
        try {
            $this->conditionRepository->create([
                [
                    'type' => (new PromotionCodeOfTypeRule())->getName(),
                    'ruleId' => Uuid::randomHex(),
                    'value' => [
                        'promotionCodeType' => true,
                        'operator' => Rule::OPERATOR_EQ,
                    ],
                ],
            ], $this->context);
            static::fail('Exception was not thrown');
        } catch (WriteException $stackException) {
            $exceptions = iterator_to_array($stackException->getErrors());
            static::assertCount(1, $exceptions);
            static::assertSame('/0/value/promotionCodeType', $exceptions[0]['source']['pointer']);
            static::assertSame(Type::INVALID_TYPE_ERROR, $exceptions[0]['code']);
        }
    }

    public function testIfRuleIsConsistent(): void
    {
        $ruleId = Uuid::randomHex();
        $this->ruleRepository->create(
            [['id' => $ruleId, 'name' => 'Demo rule', 'priority' => 1]],
            Context::createDefaultContext()
        );

        $id = Uuid::randomHex();
        $this->conditionRepository->create([
            [
                'id' => $id,
                'type' => (new PromotionCodeOfTypeRule())->getName(),
                'ruleId' => $ruleId,
                'value' => [
                    'promotionCodeType' => 'fixed',
                    'operator' => Rule::OPERATOR_EQ,
                ],
            ],
        ], $this->context);

        static::assertNotNull($this->conditionRepository->search(new Criteria([$id]), $this->context)->get($id));
    }

    /**
     * @dataProvider getCartRuleScopeTestData
     */
    public function testIfMatchesCorrectWithCartRuleScope(
        string $promotionCodeType,
        string $operator,
        ?string $typeOfPromotionCode,
        bool $expected
    ): void {
        $this->rule->assign(['promotionCodeType' => $promotionCodeType, 'operator' => $operator]);

        $lineItemCollection = new LineItemCollection();
        if ($typeOfPromotionCode !== null) {
            $lineItemCollection = new LineItemCollection([
                $this->createLineItem(LineItem::PROMOTION_LINE_ITEM_TYPE),
                ($this->createLineItem(LineItem::PROMOTION_LINE_ITEM_TYPE))->setPayloadValue(
                    'promotionCodeType',
                    $typeOfPromotionCode
                ),
            ]);
        }

        $cart = $this->createCart($lineItemCollection);

        $match = $this->rule->match(new CartRuleScope(
            $cart,
            $this->createMock(SalesChannelContext::class)
        ));

        static::assertSame($expected, $match);
    }

    /**
     * @dataProvider getCartRuleScopeTestData
     */
    public function testIfMatchesCorrectWithCartRuleScopeNested(
        string $promotionCodeType,
        string $operator,
        ?string $typeOfPromotionCode,
        bool $expected
    ): void {
        $this->rule->assign(['promotionCodeType' => $promotionCodeType, 'operator' => $operator]);

        $lineItemCollection = new LineItemCollection();
        if ($typeOfPromotionCode !== null) {
            $lineItemCollection = new LineItemCollection([
                $this->createLineItem(LineItem::PROMOTION_LINE_ITEM_TYPE),
                ($this->createLineItem(LineItem::PROMOTION_LINE_ITEM_TYPE))->setPayloadValue(
                    'promotionCodeType',
                    $typeOfPromotionCode
                ),
            ]);
        }
        $containerLineItem = $this->createContainerLineItem($lineItemCollection);
        $cart = $this->createCart(new LineItemCollection([$containerLineItem]));

        $match = $this->rule->match(new CartRuleScope(
            $cart,
            $this->createMock(SalesChannelContext::class)
        ));

        static::assertSame($expected, $match);
    }

    /**
     * @return array<string, array<string|bool|null>>
     */
    public static function getCartRuleScopeTestData(): array
    {
        return [
            'equal / match' => ['fixed', Rule::OPERATOR_EQ, 'fixed', true],
            'equal / no match' => ['test', Rule::OPERATOR_EQ, 'fixed', false],
            'not equal / match' => ['test', Rule::OPERATOR_NEQ, 'fixed', true],
            'not equal / not match' => ['fixed', Rule::OPERATOR_NEQ, 'fixed', false],
            'equal with empty cart / not match' => ['fixed', Rule::OPERATOR_EQ, null, false],
            'not equal with empty cart / match' => ['fixed', Rule::OPERATOR_NEQ, null, true],
        ];
    }
}
