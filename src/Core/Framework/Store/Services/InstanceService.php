<?php declare(strict_types=1);

namespace Laser\Core\Framework\Store\Services;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Kernel;

/**
 * @internal
 */
#[Package('merchant-services')]
class InstanceService
{
    public function __construct(
        private readonly string $laserVersion,
        private readonly ?string $instanceId
    ) {
    }

    public function getLaserVersion(): string
    {
        if ($this->laserVersion === Kernel::SHOPWARE_FALLBACK_VERSION) {
            return '___VERSION___';
        }

        return $this->laserVersion;
    }

    public function getInstanceId(): ?string
    {
        return $this->instanceId;
    }
}
