<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Document\Event;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\ExtendableTrait;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('customer-order')]
class DocumentTemplateRendererParameterEvent extends Event
{
    use ExtendableTrait;

    public function __construct(private readonly array $parameters)
    {
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
