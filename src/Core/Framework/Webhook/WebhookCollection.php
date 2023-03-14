<?php declare(strict_types=1);

namespace Laser\Core\Framework\Webhook;

use Laser\Core\Framework\DataAbstractionLayer\EntityCollection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Uuid\Uuid;

/**
 * @extends EntityCollection<WebhookEntity>
 */
#[Package('core')]
class WebhookCollection extends EntityCollection
{
    public function filterForEvent(string $name)
    {
        return $this->filterByProperty('eventName', $name);
    }

    /**
     * @return array<string>
     */
    public function getAclRoleIdsAsBinary(): array
    {
        return array_values($this->fmap(static function (WebhookEntity $webhook): ?string {
            if ($webhook->getApp()) {
                return Uuid::fromHexToBytes($webhook->getApp()->getAclRoleId());
            }

            return null;
        }));
    }

    protected function getExpectedClass(): string
    {
        return WebhookEntity::class;
    }
}
