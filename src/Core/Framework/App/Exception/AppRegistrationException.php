<?php declare(strict_types=1);

namespace Laser\Core\Framework\App\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;

/**
 * @internal only for use by the app-system, will be considered internal from v6.4.0 onward
 */
#[Package('core')]
class AppRegistrationException extends LaserHttpException
{
    public function getErrorCode(): string
    {
        return 'FRAMEWORK__APP_REGISTRATION_FAILED';
    }
}
