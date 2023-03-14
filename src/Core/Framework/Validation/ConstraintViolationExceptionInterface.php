<?php declare(strict_types=1);

namespace Laser\Core\Framework\Validation;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserException;
use Symfony\Component\Validator\ConstraintViolationList;

#[Package('core')]
interface ConstraintViolationExceptionInterface extends LaserException
{
    public function getViolations(): ConstraintViolationList;
}
