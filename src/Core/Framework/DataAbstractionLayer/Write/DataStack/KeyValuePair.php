<?php declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer\Write\DataStack;

use Laser\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
class KeyValuePair
{
    public function __construct(
        private readonly string $key,
        private mixed $value,
        private bool $isRaw
    ) {
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function isRaw(): bool
    {
        return $this->isRaw;
    }

    public function setValue(mixed $value): void
    {
        $this->isRaw = false;
        $this->value = $value;
    }
}
