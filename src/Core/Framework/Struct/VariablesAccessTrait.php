<?php declare(strict_types=1);

namespace Laser\Core\Framework\Struct;

use Laser\Core\Framework\Log\Package;

#[Package('core')]
trait VariablesAccessTrait
{
    public function getVars(): array
    {
        return get_object_vars($this);
    }
}
