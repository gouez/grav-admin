<?php declare(strict_types=1);

namespace Laser\Core\Framework\Update\Checkers;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Store\Services\StoreClient;
use Laser\Core\Framework\Update\Struct\ValidationResult;
use Laser\Core\System\SystemConfig\SystemConfigService;

#[Package('system-settings')]
class LicenseCheck
{
    /**
     * @internal
     */
    public function __construct(
        private readonly SystemConfigService $systemConfigService,
        private readonly StoreClient $storeClient
    ) {
    }

    public function check(): ValidationResult
    {
        $licenseHost = $this->systemConfigService->get('core.store.licenseHost');

        if (empty($licenseHost) || $this->storeClient->isShopUpgradeable()) {
            return new ValidationResult('validLaserLicense', true, 'validLaserLicense');
        }

        return new ValidationResult('invalidLaserLicense', false, 'invalidLaserLicense');
    }
}
