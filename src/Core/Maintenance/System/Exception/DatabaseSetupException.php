<?php declare(strict_types=1);

namespace Laser\Core\Maintenance\System\Exception;

use Laser\Core\Framework\Log\Package;

#[Package('core')]
class DatabaseSetupException extends \RuntimeException
{
}
