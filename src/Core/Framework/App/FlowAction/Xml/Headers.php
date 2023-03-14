<?php declare(strict_types=1);

namespace Laser\Core\Framework\App\FlowAction\Xml;

use Laser\Core\Framework\App\Manifest\Xml\XmlElement;
use Laser\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
class Headers extends XmlElement
{
    /**
     * @param Parameter[] $parameters
     */
    public function __construct(protected array $parameters)
    {
    }

    /**
     * @return Parameter[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public static function fromXml(\DOMElement $element): self
    {
        return new self(self::parseParameter($element));
    }

    private static function parseParameter(\DOMElement $element): array
    {
        $values = [];

        foreach ($element->getElementsByTagName('parameter') as $parameters) {
            $values[] = Parameter::fromXml($parameters);
        }

        return $values;
    }
}
