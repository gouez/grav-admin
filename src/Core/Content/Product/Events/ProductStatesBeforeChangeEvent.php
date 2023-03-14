<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\Events;

use Laser\Core\Content\Product\DataAbstractionLayer\UpdatedStates;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Event\LaserEvent;
use Laser\Core\Framework\Log\Package;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('inventory')]
class ProductStatesBeforeChangeEvent extends Event implements LaserEvent
{
    /**
     * @param UpdatedStates[] $updatedStates
     */
    public function __construct(
        protected array $updatedStates,
        protected Context $context
    ) {
    }

    /**
     * @return UpdatedStates[]
     */
    public function getUpdatedStates(): array
    {
        return $this->updatedStates;
    }

    /**
     * @param UpdatedStates[] $updatedStates
     */
    public function setUpdatedStates(array $updatedStates): void
    {
        $this->updatedStates = $updatedStates;
    }

    public function getContext(): Context
    {
        return $this->context;
    }
}
