<?php declare(strict_types=1);

namespace Laser\Core\Framework\Webhook;

use Laser\Core\Framework\App\Event\AppActivatedEvent;
use Laser\Core\Framework\App\Event\AppDeactivatedEvent;
use Laser\Core\Framework\App\Event\AppDeletedEvent;
use Laser\Core\Framework\App\Event\AppInstalledEvent;
use Laser\Core\Framework\App\Event\AppUpdatedEvent;
use Laser\Core\Framework\Log\Package;

#[Package('core')]
interface Hookable
{
    public const HOOKABLE_EVENTS = [
        AppActivatedEvent::class => AppActivatedEvent::NAME,
        AppDeactivatedEvent::class => AppDeactivatedEvent::NAME,
        AppDeletedEvent::class => AppDeletedEvent::NAME,
        AppInstalledEvent::class => AppInstalledEvent::NAME,
        AppUpdatedEvent::class => AppUpdatedEvent::NAME,
    ];

    public function getName(): string;

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint
     */
    public function getWebhookPayload(): array;

    /**
     * returns if it is allowed to dispatch the event to given app with given permissions
     */
    public function isAllowed(string $appId, AclPrivilegeCollection $permissions): bool;
}
