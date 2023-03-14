<?php declare(strict_types=1);

namespace Laser\Core\Framework\Adapter\Cache;

use Laser\Core\Framework\Log\Package;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('core')]
class InvalidateCacheEvent extends Event
{
    public function __construct(protected array $keys)
    {
    }

    public function getKeys(): array
    {
        return $this->keys;
    }
}
