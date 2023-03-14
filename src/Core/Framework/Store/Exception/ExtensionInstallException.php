<?php declare(strict_types=1);

namespace Laser\Core\Framework\Store\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;

#[Package('merchant-services')]
class ExtensionInstallException extends LaserHttpException
{
    public function getErrorCode(): string
    {
        return 'FRAMEWORK__EXTENSION_INSTALL_EXCEPTION';
    }
}
