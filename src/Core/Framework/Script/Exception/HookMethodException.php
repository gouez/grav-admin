<?php declare(strict_types=1);

namespace Laser\Core\Framework\Script\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;

#[Package('core')]
class HookMethodException extends LaserHttpException
{
    public static function outsideOfSalesChannelContext(string $method): self
    {
        return new self(sprintf(
            'Method "%s" can only be called from inside the `SalesChannelContext`.',
            $method
        ));
    }

    public static function storefrontBundleMissing(string $method): self
    {
        return new self(sprintf(
            'Method "%s" can only be called if the `storefront`-bundle is installed.',
            $method
        ));
    }

    public static function accessFromScriptExecutionContextNotAllowed(string $class, string $method): self
    {
        return new self(sprintf(
            'Method "%s" of class "%s" can not be called from inside a script.',
            $method,
            $class
        ));
    }

    public static function functionDoesNotExistInInterfaceHook(string $class, string $function): self
    {
        return new self(sprintf(
            'Function "%s" does not exist for InterfaceHook "%s".',
            $function,
            $class
        ));
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__HOOK_METHOD_EXCEPTION';
    }
}
