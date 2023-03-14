<?php declare(strict_types=1);

namespace Laser\Core\Framework\Adapter\Twig\Extension;

use Laser\Core\Framework\Adapter\Twig\TemplateFinder;
use Laser\Core\Framework\Adapter\Twig\TokenParser\ExtendsTokenParser;
use Laser\Core\Framework\Adapter\Twig\TokenParser\IncludeTokenParser;
use Laser\Core\Framework\Adapter\Twig\TokenParser\ReturnNodeTokenParser;
use Laser\Core\Framework\Log\Package;
use Twig\Extension\AbstractExtension;

#[Package('core')]
class NodeExtension extends AbstractExtension
{
    /**
     * @internal
     */
    public function __construct(private readonly TemplateFinder $finder)
    {
    }

    public function getTokenParsers(): array
    {
        return [
            new ExtendsTokenParser($this->finder),
            new IncludeTokenParser($this->finder),
            new ReturnNodeTokenParser(),
        ];
    }

    public function getFinder(): TemplateFinder
    {
        return $this->finder;
    }
}
