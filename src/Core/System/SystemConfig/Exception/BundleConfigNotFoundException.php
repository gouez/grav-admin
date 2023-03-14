<?php declare(strict_types=1);

namespace Laser\Core\System\SystemConfig\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;

#[Package('system-settings')]
class BundleConfigNotFoundException extends LaserHttpException
{
    public function __construct(
        string $configPath,
        string $bundleName
    ) {
        parent::__construct(
            'Could not find "{{ configPath }}" for bundle "{{ bundle }}".',
            [
                'configPath' => $configPath,
                'bundle' => $bundleName,
            ]
        );
    }

    public function getErrorCode(): string
    {
        return 'SYSTEM__BUNDLE_CONFIG_NOT_FOUND';
    }
}
