<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Customer\Rule;

use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\CheckoutRuleScope;
use Laser\Core\Checkout\Customer\CustomerEntity;
use Laser\Core\Checkout\Customer\Rule\OrderCountRule;
use Laser\Core\Checkout\Order\OrderCollection;
use Laser\Core\Checkout\Order\OrderEntity;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Write\WriteException;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Rule\Rule;
use Laser\Core\Framework\Rule\RuleScope;
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
class OrderCountRuleTest extends TestCase
{
    use KernelTestBehaviour;
    use DatabaseTransactionBehaviour;

    private EntityRepository $ruleRepository;

    private EntityRepository $conditionRepository;

    private Context $context;

    protected function setUp(): void
    {
        $this->ruleRepository = $this->getContainer()->get('rule.repository');
        $this->conditionRepository = $this->getContainer()->get('rule_condition.repository');
        $this->context = Context::createDefaultContext();
    }

    public function testValidateWithMissingValues(): void
    {
        try {
            $this->conditionRepository->create([
                [
                    'type' => (new OrderCountRule())->getName(),
                    'ruleId' => Uuid::randomHex(),
                ],
            ], $this->context);
            static::fail('Exception was not thrown');
        } catch (WriteException $stackException) {
            $exceptions = iterator_to_array($stackException->getErrors());
            static::assertCount(2, $exceptions);
            static::assertSame('/0/value/count', $exceptions[0]['source']['pointer']);
            static::assertSame(NotBlank::IS_BLANK_ERROR, $exceptions[0]['code']);

            static::assertSame('/0/value/operator', $exceptions[1]['source']['pointer']);
            static::assertSame(NotBlank::IS_BLANK_ERROR, $exceptions[1]['code']);
        }
    }

    public function testValidateWithEmptyValues(): void
    {
        try {
            $this->conditionRepository->create([
                [
                    'type' => (new OrderCountRule())->getName(),
                    'ruleId' => Uuid::randomHex(),
                    'value' => [
                        'operator' => OrderCountRule::OPERATOR_EQ,
                        'count' => null,
                    ],
                ],
            ], $this->context);
            static::fail('Exception was not thrown');
        } catch (WriteException $stackException) {
            $exceptions = iterator_to_array($stackException->getErrors());
            static::assertCount(1, $exceptions);
            static::assertSame('/0/value/count', $exceptions[0]['source']['pointer']);
            static::assertSame(NotBlank::IS_BLANK_ERROR, $exceptions[0]['code']);
        }
    }

    public function testValidateWithStringValue(): void
    {
        try {
            $this->conditionRepository->create([
                [
                    'type' => (new OrderCountRule())->getName(),
                    'ruleId' => Uuid::randomHex(),
                    'value' => [
                        'operator' => OrderCountRule::OPERATOR_EQ,
                        'count' => '4',
                    ],
                ],
            ], $this->context);
            static::fail('Exception was not thrown');
        } catch (WriteException $stackException) {
            $exceptions = iterator_to_array($stackException->getErrors());
            static::assertCount(1, $exceptions);
            static::assertSame('/0/value/count', $exceptions[0]['source']['pointer']);
            static::assertSame(Type::INVALID_TYPE_ERROR, $exceptions[0]['code']);
        }
    }

    public function testValidateWithInvalidValue(): void
    {
        try {
            $this->conditionRepository->create([
                [
                    'type' => (new OrderCountRule())->getName(),
                    'ruleId' => Uuid::randomHex(),
                    'value' => [
                        'operator' => OrderCountRule::OPERATOR_EQ,
                        'count' => true,
                    ],
                ],
            ], $this->context);
            static::fail('Exception was not thrown');
        } catch (WriteException $stackException) {
            $exceptions = iterator_to_array($stackException->getErrors());
            static::assertCount(1, $exceptions);
            static::assertSame('/0/value/count', $exceptions[0]['source']['pointer']);
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
                'type' => (new OrderCountRule())->getName(),
                'ruleId' => $ruleId,
                'value' => [
                    'operator' => OrderCountRule::OPERATOR_EQ,
                    'count' => 6,
                ],
            ],
        ], $this->context);

        static::assertNotNull($this->conditionRepository->search(new Criteria([$id]), $this->context)->get($id));
    }

    public function testRuleDoesNotMatchWithWrongScope(): void
    {
        $rule = new OrderCountRule();
        $rule->assign(['count' => 2, 'operator' => Rule::OPERATOR_LT]);

        $result = $rule->match($this->getMockForAbstractClass(RuleScope::class));

        static::assertFalse($result);
    }

    /**
     * @dataProvider getMatchValues
     */
    public function testRuleMatching(string $operator, bool $isMatching, ?int $orderCount, int $ruleOrderCount, bool $noCustomer = false): void
    {
        $rule = new OrderCountRule();
        $rule->assign(['count' => $ruleOrderCount, 'operator' => $operator]);

        $scope = $this->createMock(CheckoutRuleScope::class);
        $salesChannelContext = $this->createMock(SalesChannelContext::class);
        $orderCollection = new OrderCollection();
        $customer = new CustomerEntity();
        $customer->setOrderCount($orderCount ?? 0);

        if ($noCustomer) {
            $customer = null;
        }

        $salesChannelContext->method('getCustomer')->willReturn($customer);
        $entity = new OrderEntity();
        $entity->setUniqueIdentifier('test');
        $orderCollection->add($entity);

        $scope->method('getSalesChannelContext')
            ->willReturn($salesChannelContext);

        static::assertSame($isMatching, $rule->match($scope));
    }

    /**
     * @return \Traversable<string, array<string|bool|int>>
     */
    public static function getMatchValues(): \Traversable
    {
        yield 'operator_eq / no match / greater value' => [Rule::OPERATOR_EQ, false, 100, 50];
        yield 'operator_eq / match / equal value' => [Rule::OPERATOR_EQ, true, 50, 50];
        yield 'operator_eq / no match / lower value' => [Rule::OPERATOR_EQ, false, 10, 50];
        yield 'operator_eq / no match / no customer' => [Rule::OPERATOR_EQ, false, 100, 50, true];

        yield 'operator_gt / match / greater value' => [Rule::OPERATOR_GT, true, 100, 50];
        yield 'operator_gt / no match / equal value' => [Rule::OPERATOR_GT, false, 50, 50];
        yield 'operator_gt / no match / lower value' => [Rule::OPERATOR_GT, false, 10, 50];
        yield 'operator_gt / no match / no customer' => [Rule::OPERATOR_GT, false, 100, 50, true];

        yield 'operator_gte / match / greater value' => [Rule::OPERATOR_GTE, true, 100, 50];
        yield 'operator_gte / match / equal value' => [Rule::OPERATOR_GTE, true, 50, 50];
        yield 'operator_gte / no match / lower value' => [Rule::OPERATOR_GTE, false, 10, 50];
        yield 'operator_gte / no match / no customer' => [Rule::OPERATOR_GTE, false, 100, 50, true];

        yield 'operator_lt / no match / greater value' => [Rule::OPERATOR_LT, false, 100, 50];
        yield 'operator_lt / no match / equal value' => [Rule::OPERATOR_LT, false, 50, 50];
        yield 'operator_lt / match / lower value' => [Rule::OPERATOR_LT, true, 10, 50];
        yield 'operator_lt / no match / no customer' => [Rule::OPERATOR_LT, false, 10, 50, true];

        yield 'operator_lte / no match / greater value' => [Rule::OPERATOR_LTE, false, 100, 50];
        yield 'operator_lte / match / equal value' => [Rule::OPERATOR_LTE, true, 50, 50];
        yield 'operator_lte / match / lower value' => [Rule::OPERATOR_LTE, true, 10, 50];
        yield 'operator_lte / no match / no customer' => [Rule::OPERATOR_LTE, false, 10, 50, true];

        yield 'operator_neq / match / greater value' => [Rule::OPERATOR_NEQ, true, 100, 50];
        yield 'operator_neq / no match / equal value' => [Rule::OPERATOR_NEQ, false, 50, 50];
        yield 'operator_neq / match / lower value' => [Rule::OPERATOR_NEQ, true, 10, 50];

        yield 'operator_neq / match / no customer' => [Rule::OPERATOR_NEQ, true, 100, 50, true];
    }
}
