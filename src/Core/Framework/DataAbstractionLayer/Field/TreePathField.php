<?php declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer\Field;

use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\WriteProtected;
use Laser\Core\Framework\Log\Package;

#[Package('core')]
class TreePathField extends LongTextField
{
    public function __construct(
        string $storageName,
        string $propertyName,
        private readonly string $pathField = 'id'
    ) {
        parent::__construct($storageName, $propertyName);

        $this->addFlags(new WriteProtected(Context::SYSTEM_SCOPE));
    }

    public function getPathField(): string
    {
        return $this->pathField;
    }
}
