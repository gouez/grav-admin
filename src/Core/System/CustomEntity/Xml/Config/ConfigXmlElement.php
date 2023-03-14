<?php declare(strict_types=1);

namespace Laser\Core\System\CustomEntity\Xml\Config;

use Laser\Core\Framework\App\Manifest\Xml\XmlElement;
use Laser\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('content')]
abstract class ConfigXmlElement extends XmlElement
{
    abstract public static function fromXml(\DOMElement $element): self;

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize();
        unset($data['extensions']);

        return $data;
    }
}
