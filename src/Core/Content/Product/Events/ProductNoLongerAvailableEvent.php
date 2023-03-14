<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\Events;

use Laser\Core\Framework\Context;
use Laser\Core\Framework\Event\LaserEvent;
use Laser\Core\Framework\Log\Package;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('inventory')]
class ProductNoLongerAvailableEvent extends Event implements LaserEvent, ProductChangedEventInterface
{
    public function __construct(
        protected array $ids,
        protected Context $context
    ) {
    }

    public function getIds(): array
    {
        return $this->ids;
    }

    public function getContext(): Context
    {
        return $this->context;
    }
}
