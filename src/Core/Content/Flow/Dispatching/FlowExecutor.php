<?php declare(strict_types=1);

namespace Laser\Core\Content\Flow\Dispatching;

use Laser\Core\Checkout\Cart\AbstractRuleLoader;
use Laser\Core\Checkout\Order\OrderEntity;
use Laser\Core\Content\Flow\Dispatching\Action\FlowAction;
use Laser\Core\Content\Flow\Dispatching\Struct\ActionSequence;
use Laser\Core\Content\Flow\Dispatching\Struct\Flow;
use Laser\Core\Content\Flow\Dispatching\Struct\IfSequence;
use Laser\Core\Content\Flow\Dispatching\Struct\Sequence;
use Laser\Core\Content\Flow\Exception\ExecuteSequenceException;
use Laser\Core\Content\Flow\Rule\FlowRuleScopeBuilder;
use Laser\Core\Framework\App\Event\AppFlowActionEvent;
use Laser\Core\Framework\App\FlowAction\AppFlowActionProvider;
use Laser\Core\Framework\Event\OrderAware;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Rule\Rule;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @internal not intended for decoration or replacement
 */
#[Package('business-ops')]
class FlowExecutor
{
    /**
     * @var array<string, mixed>
     */
    private readonly array $actions;

    /**
     * @param FlowAction[] $actions
     */
    public function __construct(
        private readonly EventDispatcherInterface $dispatcher,
        private readonly AppFlowActionProvider $appFlowActionProvider,
        private readonly AbstractRuleLoader $ruleLoader,
        private readonly FlowRuleScopeBuilder $scopeBuilder,
        $actions
    ) {
        $this->actions = $actions instanceof \Traversable ? iterator_to_array($actions) : $actions;
    }

    public function execute(Flow $flow, StorableFlow $event): void
    {
        $state = new FlowState();

        $event->setFlowState($state);
        $state->flowId = $flow->getId();
        foreach ($flow->getSequences() as $sequence) {
            $state->delayed = false;

            try {
                $this->executeSequence($sequence, $event);
            } catch (\Exception $e) {
                throw new ExecuteSequenceException($sequence->flowId, $sequence->sequenceId, $e->getMessage(), $e->getCode(), $e);
            }

            if ($state->stop) {
                return;
            }
        }
    }

    public function executeSequence(?Sequence $sequence, StorableFlow $event): void
    {
        if ($sequence === null) {
            return;
        }

        $event->getFlowState()->currentSequence = $sequence;

        if ($sequence instanceof IfSequence) {
            $this->executeIf($sequence, $event);

            return;
        }

        if ($sequence instanceof ActionSequence) {
            $this->executeAction($sequence, $event);
        }
    }

    public function executeAction(ActionSequence $sequence, StorableFlow $event): void
    {
        $actionName = $sequence->action;
        if (!$actionName) {
            return;
        }

        if ($event->getFlowState()->stop) {
            return;
        }

        $event->setConfig($sequence->config);

        $this->callHandle($sequence, $event);

        if ($event->getFlowState()->delayed) {
            return;
        }

        $event->getFlowState()->currentSequence = $sequence;

        /** @var ActionSequence $nextAction */
        $nextAction = $sequence->nextAction;
        if ($nextAction !== null) {
            $this->executeAction($nextAction, $event);
        }
    }

    public function executeIf(IfSequence $sequence, StorableFlow $event): void
    {
        if ($this->sequenceRuleMatches($event, $sequence->ruleId)) {
            $this->executeSequence($sequence->trueCase, $event);

            return;
        }

        $this->executeSequence($sequence->falseCase, $event);
    }

    private function callHandle(ActionSequence $sequence, StorableFlow $event): void
    {
        if ($sequence->appFlowActionId) {
            $this->callApp($sequence, $event);

            return;
        }

        $action = $this->actions[$sequence->action] ?? null;
        $action?->handleFlow($event);
    }

    private function callApp(ActionSequence $sequence, StorableFlow $event): void
    {
        if (!$sequence->appFlowActionId) {
            return;
        }

        $eventData = $this->appFlowActionProvider->getWebhookPayloadAndHeaders($event, $sequence->appFlowActionId);

        $globalEvent = new AppFlowActionEvent(
            $sequence->action,
            $eventData['headers'],
            $eventData['payload'],
        );

        $this->dispatcher->dispatch($globalEvent, $sequence->action);
    }

    private function sequenceRuleMatches(StorableFlow $event, string $ruleId): bool
    {
        if (!$event->hasData(OrderAware::ORDER)) {
            return \in_array($ruleId, $event->getContext()->getRuleIds(), true);
        }

        $order = $event->getData(OrderAware::ORDER);

        if (!$order instanceof OrderEntity) {
            return \in_array($ruleId, $event->getContext()->getRuleIds(), true);
        }

        $rule = $this->ruleLoader->load($event->getContext())->filterForFlow()->get($ruleId);

        if (!$rule || !$rule->getPayload() instanceof Rule) {
            return \in_array($ruleId, $event->getContext()->getRuleIds(), true);
        }

        return $rule->getPayload()->match($this->scopeBuilder->build($order, $event->getContext()));
    }
}
