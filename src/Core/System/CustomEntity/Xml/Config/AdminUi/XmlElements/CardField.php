<?php declare(strict_types=1);

namespace Laser\Core\System\CustomEntity\Xml\Config\AdminUi\XmlElements;

use Laser\Core\Framework\Log\Package;
use Laser\Core\System\CustomEntity\Xml\Config\ConfigXmlElement;
use Symfony\Component\Config\Util\XmlUtils;

/**
 * Represents the XML field element
 *
 * admin-ui > entity > detail > tabs > tab > card > field
 *
 * @internal
 */
#[Package('content')]
final class CardField extends ConfigXmlElement
{
    private function __construct(
        protected readonly string $ref
    ) {
    }

    public static function fromXml(\DOMElement $element): self
    {
        return new self(
            XmlUtils::phpize($element->getAttribute('ref'))
        );
    }

    public function getRef(): string
    {
        return $this->ref;
    }
}
