<?php declare(strict_types=1);

namespace Laser\Core\System\CustomEntity\Xml\Field;

use Laser\Core\Framework\Log\Package;
use Laser\Core\System\CustomEntity\Xml\Field\Traits\RequiredTrait;

#[Package('core')]
class PriceField extends Field
{
    use RequiredTrait;

    protected string $type = 'price';

    /**
     * @internal
     */
    public static function fromXml(\DOMElement $element): Field
    {
        return new self(self::parse($element));
    }
}
