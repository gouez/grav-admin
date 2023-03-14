<?php declare(strict_types=1);

namespace Laser\Core\Framework\App\FlowAction\Xml;

use Laser\Core\Framework\App\Manifest\Xml\XmlElement;
use Laser\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
class Config extends XmlElement
{
    /**
     * @param InputField[] $config
     */
    public function __construct(protected array $config)
    {
    }

    /**
     * @return InputField[]
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    public static function fromXml(\DOMElement $element): self
    {
        return new self(self::parseInputField($element));
    }

    private static function parseInputField(\DOMElement $element): array
    {
        $values = [];

        foreach ($element->getElementsByTagName('input-field') as $parameter) {
            $values[] = InputField::fromXml($parameter);
        }

        return $values;
    }
}
