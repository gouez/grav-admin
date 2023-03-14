<?php declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer\Event;

use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Laser\Core\Framework\DataAbstractionLayer\PartialEntity;
use Laser\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
class PartialEntityLoadedEvent extends EntityLoadedEvent
{
    /**
     * @var PartialEntity[]
     */
    protected $entities;

    /**
     * @param PartialEntity[] $entities
     */
    public function __construct(
        EntityDefinition $definition,
        array $entities,
        Context $context
    ) {
        parent::__construct($definition, $entities, $context);
        $this->name = $this->definition->getEntityName() . '.partial_loaded';
    }

    /**
     * @return PartialEntity[]
     */
    public function getEntities(): array
    {
        return $this->entities;
    }
}
