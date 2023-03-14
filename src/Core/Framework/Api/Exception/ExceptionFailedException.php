<?php declare(strict_types=1);

namespace Laser\Core\Framework\Api\Exception;

use Laser\Core\Framework\Log\Package;

/**
 * @deprecated tag:v6.6.0 - Will be removed, use Laser\Core\Framework\Api\Exception\ExpectationFailedException instead
 */
#[Package('core')]
class ExceptionFailedException extends ExpectationFailedException
{
}
