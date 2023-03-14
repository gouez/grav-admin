<?php declare(strict_types=1);

namespace Laser\Core\Framework\App\FlowAction\Xml;

use Laser\Core\Framework\App\Manifest\Xml\XmlElement;
use Laser\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
class Actions extends XmlElement
{
    /**
     * @param Action[] $actions
     */
    public function __construct(protected array $actions)
    {
    }

    public static function fromXml(\DOMElement $element): self
    {
        return new self(self::parseFlowActions($element));
    }

    /**
     * @return Action[]
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    private static function parseFlowActions(\DOMElement $element): array
    {
        $actions = [];
        foreach ($element->getElementsByTagName('flow-action') as $flowAction) {
            $actions[] = Action::fromXml($flowAction);
        }

        return $actions;
    }
}
