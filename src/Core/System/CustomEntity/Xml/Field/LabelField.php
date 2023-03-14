<?php declare(strict_types=1);

namespace Laser\Core\System\CustomEntity\Xml\Field;

use Laser\Core\Framework\Log\Package;

#[Package('core')]
class LabelField extends Field
{
    protected string $type = 'label';

    /**
     * @internal
     */
    public static function fromXml(\DOMElement $element): Field
    {
        return new self(self::parse($element));
    }
}
