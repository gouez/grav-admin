<?php declare(strict_types=1);

namespace Laser\Core\Framework\Plugin\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;

#[Package('core')]
class CanNotDeletePluginManagedByComposerException extends LaserHttpException
{
    public function __construct(string $reason)
    {
        parent::__construct(
            'Can not delete plugin. Please contact your system administrator. Error: {{ reason }}',
            ['reason' => $reason]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__STORE_CANNOT_DELETE_PLUGIN_MANAGED_BY_SHOPWARE';
    }
}
