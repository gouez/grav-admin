<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\Address\Error;

use Laser\Core\Checkout\Cart\Error\Error;
use Laser\Core\Framework\Log\Package;

#[Package('checkout')]
abstract class SalutationMissingError extends Error
{
    protected const KEY = 'salutation-missing';

    protected array $parameters;

    abstract public function getId(): string;

    public function getMessageKey(): string
    {
        return $this->getId();
    }

    public function getLevel(): int
    {
        return Error::LEVEL_WARNING;
    }

    public function blockOrder(): bool
    {
        return true;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
