<?php declare(strict_types=1);

namespace Laser\Core\Content\Flow\Aggregate\FlowTemplate;

use Laser\Core\Framework\DataAbstractionLayer\Entity;
use Laser\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Laser\Core\Framework\Log\Package;

#[Package('business-ops')]
class FlowTemplateEntity extends Entity
{
    use EntityIdTrait;

    protected string $name;

    /**
     * @var array<string, mixed>
     */
    protected array $config;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return array<string, mixed>
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @param array<string, mixed> $config
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }
}
