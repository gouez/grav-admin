<?php declare(strict_types=1);

namespace Laser\Core\System\NumberRange\DataAbstractionLayer;

use Laser\Core\Framework\DataAbstractionLayer\Field\StringField;
use Laser\Core\Framework\Log\Package;

#[Package('core')]
class NumberRangeField extends StringField
{
    public function __construct(
        string $storageName,
        string $propertyName,
        int $maxLength = 64
    ) {
        parent::__construct($storageName, $propertyName, $maxLength);
    }
}
