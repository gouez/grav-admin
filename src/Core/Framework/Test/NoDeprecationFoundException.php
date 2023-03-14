<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test;

use Laser\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
class NoDeprecationFoundException extends \Exception
{
}
