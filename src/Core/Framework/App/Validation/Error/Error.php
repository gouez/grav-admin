<?php declare(strict_types=1);

namespace Laser\Core\Framework\App\Validation\Error;

use Laser\Core\Framework\Log\Package;

/**
 * @internal only for use by the app-system
 */
#[Package('core')]
abstract class Error extends \Exception
{
    abstract public function getMessageKey(): string;
}
