<?php declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer\Event;

use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Laser\Core\Framework\DataAbstractionLayer\Search\AggregationResult\AggregationResultCollection;
use Laser\Core\Framework\Event\GenericEvent;
use Laser\Core\Framework\Event\NestedEvent;
use Laser\Core\Framework\Log\Package;

#[Package('core')]
class EntityAggregationResultLoadedEvent extends NestedEvent implements GenericEvent
{
    /**
     * @var AggregationResultCollection
     */
    protected $result;

    /**
     * @var EntityDefinition
     */
    protected $definition;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Context
     */
    protected $context;

    public function __construct(
        EntityDefinition $definition,
        AggregationResultCollection $result,
        Context $context
    ) {
        $this->result = $result;
        $this->definition = $definition;
        $this->name = $this->definition->getEntityName() . '.aggregation.result.loaded';
        $this->context = $context;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function getResult(): AggregationResultCollection
    {
        return $this->result;
    }
}
