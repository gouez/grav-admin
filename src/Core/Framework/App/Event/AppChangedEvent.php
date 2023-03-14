<?php declare(strict_types=1);

namespace Laser\Core\Framework\App\Event;

use Laser\Core\Framework\App\AppEntity;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Event\LaserEvent;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Webhook\AclPrivilegeCollection;
use Laser\Core\Framework\Webhook\Hookable;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @internal only for use by the app-system, will be considered internal from v6.4.0 onward
 */
#[Package('core')]
abstract class AppChangedEvent extends Event implements LaserEvent, Hookable
{
    public function __construct(
        private readonly AppEntity $app,
        private readonly Context $context
    ) {
    }

    abstract public function getName(): string;

    public function getApp(): AppEntity
    {
        return $this->app;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function getWebhookPayload(): array
    {
        return [];
    }

    public function isAllowed(string $appId, AclPrivilegeCollection $permissions): bool
    {
        return $appId === $this->app->getId();
    }
}
