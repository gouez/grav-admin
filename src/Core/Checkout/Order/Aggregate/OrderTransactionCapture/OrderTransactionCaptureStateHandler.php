<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Order\Aggregate\OrderTransactionCapture;

use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\Exception\InconsistentCriteriaIdsException;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\StateMachine\Aggregation\StateMachineTransition\StateMachineTransitionActions;
use Laser\Core\System\StateMachine\Exception\IllegalTransitionException;
use Laser\Core\System\StateMachine\Exception\StateMachineInvalidEntityIdException;
use Laser\Core\System\StateMachine\Exception\StateMachineInvalidStateFieldException;
use Laser\Core\System\StateMachine\Exception\StateMachineNotFoundException;
use Laser\Core\System\StateMachine\StateMachineRegistry;
use Laser\Core\System\StateMachine\Transition;

#[Package('customer-order')]
class OrderTransactionCaptureStateHandler
{
    /**
     * @internal
     */
    public function __construct(private readonly StateMachineRegistry $stateMachineRegistry)
    {
    }

    /**
     * @throws InconsistentCriteriaIdsException
     * @throws StateMachineNotFoundException
     * @throws IllegalTransitionException
     * @throws StateMachineInvalidEntityIdException
     * @throws StateMachineInvalidStateFieldException
     */
    public function complete(string $transactionCaptureId, Context $context): void
    {
        $this->stateMachineRegistry->transition(
            new Transition(
                OrderTransactionCaptureDefinition::ENTITY_NAME,
                $transactionCaptureId,
                StateMachineTransitionActions::ACTION_COMPLETE,
                'stateId'
            ),
            $context
        );
    }

    /**
     * @throws InconsistentCriteriaIdsException
     * @throws StateMachineNotFoundException
     * @throws IllegalTransitionException
     * @throws StateMachineInvalidEntityIdException
     * @throws StateMachineInvalidStateFieldException
     */
    public function fail(string $transactionCaptureId, Context $context): void
    {
        $this->stateMachineRegistry->transition(
            new Transition(
                OrderTransactionCaptureDefinition::ENTITY_NAME,
                $transactionCaptureId,
                StateMachineTransitionActions::ACTION_FAIL,
                'stateId'
            ),
            $context
        );
    }

    /**
     * @throws InconsistentCriteriaIdsException
     * @throws StateMachineNotFoundException
     * @throws IllegalTransitionException
     * @throws StateMachineInvalidEntityIdException
     * @throws StateMachineInvalidStateFieldException
     */
    public function reopen(string $transactionCaptureId, Context $context): void
    {
        $this->stateMachineRegistry->transition(
            new Transition(
                OrderTransactionCaptureDefinition::ENTITY_NAME,
                $transactionCaptureId,
                StateMachineTransitionActions::ACTION_REOPEN,
                'stateId'
            ),
            $context
        );
    }
}
