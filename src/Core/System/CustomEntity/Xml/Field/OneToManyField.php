<?php declare(strict_types=1);

namespace Laser\Core\System\CustomEntity\Xml\Field;

use Laser\Core\Framework\Log\Package;

#[Package('core')]
class OneToManyField extends AssociationField
{
    protected string $type = 'one-to-many';

    protected bool $reverseRequired = false;

    public static function fromXml(\DOMElement $element): Field
    {
        return new self(self::parse($element));
    }

    public function isReverseRequired(): bool
    {
        return $this->reverseRequired;
    }
}
