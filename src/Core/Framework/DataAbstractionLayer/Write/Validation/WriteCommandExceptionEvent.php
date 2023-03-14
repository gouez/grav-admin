<?php declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer\Write\Validation;

use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\Write\Command\WriteCommand;
use Laser\Core\Framework\Event\LaserEvent;
use Laser\Core\Framework\Log\Package;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('core')]
class WriteCommandExceptionEvent extends Event implements LaserEvent
{
    /**
     * @param WriteCommand[] $commands
     */
    public function __construct(
        private readonly \Throwable $exception,
        private readonly array $commands,
        private readonly Context $context
    ) {
    }

    public function getException(): \Throwable
    {
        return $this->exception;
    }

    public function getCommands(): array
    {
        return $this->commands;
    }

    public function getContext(): Context
    {
        return $this->context;
    }
}
