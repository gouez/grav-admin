<?php declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer\Write\FieldException;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserException;

#[Package('core')]
interface WriteFieldException extends LaserException
{
    public function getPath(): string;
}
