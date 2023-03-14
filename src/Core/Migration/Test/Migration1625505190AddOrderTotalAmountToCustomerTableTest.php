<?php declare(strict_types=1);

namespace Laser\Core\Migration\Test;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\Customer\CustomerEntity;
use Laser\Core\Checkout\Test\Customer\Rule\OrderFixture;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Test\TestCaseBase\DatabaseTransactionBehaviour;
use Laser\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\Migration\V6_4\Migration1625505190AddOrderTotalAmountToCustomerTable;
use Laser\Core\System\StateMachine\Aggregation\StateMachineTransition\StateMachineTransitionActions;
use Laser\Core\System\StateMachine\StateMachineRegistry;
use Laser\Core\System\StateMachine\Transition;

/**
 * @internal
 * NEXT-21735
 *
 * @group not-deterministic
 */
#[Package('core')]
class Migration1625505190AddOrderTotalAmountToCustomerTableTest extends TestCase
{
    use KernelTestBehaviour;
    use DatabaseTransactionBehaviour;
    use OrderFixture;

    public function testUpdateOrderTotalAmount(): void
    {
        /** @var EntityRepository $orderRepository */
        $orderRepository = $this->getContainer()->get('order.repository');
        $defaultContext = Context::createDefaultContext();
        $orderId = Uuid::randomHex();
        $orderData = $this->getOrderData($orderId, $defaultContext);

        $orderRepository->create($orderData, $defaultContext);
        $this->getContainer()->get(StateMachineRegistry::class)->transition(
            new Transition(
                'order',
                $orderId,
                StateMachineTransitionActions::ACTION_PROCESS,
                'stateId',
            ),
            $defaultContext
        );

        $this->getContainer()->get(StateMachineRegistry::class)->transition(
            new Transition(
                'order',
                $orderId,
                StateMachineTransitionActions::ACTION_COMPLETE,
                'stateId',
            ),
            $defaultContext
        );

        $migration = new Migration1625505190AddOrderTotalAmountToCustomerTable();
        $migration->update($this->getContainer()->get(Connection::class));

        $criteria = new Criteria([$orderData[0]['orderCustomer']['customer']['id']]);

        /** @var CustomerEntity $customer */
        $customer = $this->getContainer()->get('customer.repository')->search($criteria, $defaultContext)->first();

        static::assertEquals(10, $customer->getOrderTotalAmount());
    }
}
