<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Customer\Rule;

use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\CheckoutRuleScope;
use Laser\Core\Checkout\Customer\Aggregate\CustomerAddress\CustomerAddressEntity;
use Laser\Core\Checkout\Customer\CustomerEntity;
use Laser\Core\Checkout\Customer\Rule\DifferentAddressesRule;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Test\TestCaseBase\DatabaseTransactionBehaviour;
use Laser\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\System\SalesChannel\SalesChannelContext;

/**
 * @internal
 */
#[Package('business-ops')]
class DifferentAddressesRuleTest extends TestCase
{
    use KernelTestBehaviour;
    use DatabaseTransactionBehaviour;

    /**
     * @var EntityRepository
     */
    private $ruleRepository;

    /**
     * @var EntityRepository
     */
    private $conditionRepository;

    private Context $context;

    protected function setUp(): void
    {
        $this->ruleRepository = $this->getContainer()->get('rule.repository');
        $this->conditionRepository = $this->getContainer()->get('rule_condition.repository');
        $this->context = Context::createDefaultContext();
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
                'type' => (new DifferentAddressesRule())->getName(),
                'ruleId' => $ruleId,
                'value' => [
                    'isDifferent' => true,
                ],
            ],
        ], $this->context);

        static::assertNotNull($this->conditionRepository->search(new Criteria([$id]), $this->context)->get($id));
    }

    public function testRuleNotMatchingWithoutAddresses(): void
    {
        $rule = new DifferentAddressesRule();
        $salesChannelContext = $this->createMock(SalesChannelContext::class);

        static::assertFalse($rule->match(new CheckoutRuleScope($salesChannelContext)));

        $customer = new CustomerEntity();
        $salesChannelContext->method('getCustomer')->willReturn($customer);

        static::assertFalse($rule->match(new CheckoutRuleScope($salesChannelContext)));

        $customerAddress = new CustomerAddressEntity();
        $customer->setActiveBillingAddress($customerAddress);
        $salesChannelContext->method('getCustomer')->willReturn($customer);

        static::assertFalse($rule->match(new CheckoutRuleScope($salesChannelContext)));
    }
}
