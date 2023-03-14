<?php declare(strict_types=1);

namespace Laser\Core\Content\Property\Aggregate\PropertyGroupOptionTranslation;

use Laser\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionEntity;
use Laser\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use Laser\Core\Framework\DataAbstractionLayer\TranslationEntity;
use Laser\Core\Framework\Log\Package;

#[Package('inventory')]
class PropertyGroupOptionTranslationEntity extends TranslationEntity
{
    use EntityCustomFieldsTrait;

    /**
     * @var string
     */
    protected $propertyGroupOptionId;

    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var int|null
     */
    protected $position;

    /**
     * @var PropertyGroupOptionEntity|null
     */
    protected $propertyGroupOption;

    public function getPropertyGroupOptionId(): string
    {
        return $this->propertyGroupOptionId;
    }

    public function setPropertyGroupOptionId(string $propertyGroupOptionId): void
    {
        $this->propertyGroupOptionId = $propertyGroupOptionId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getPropertyGroupOption(): ?PropertyGroupOptionEntity
    {
        return $this->propertyGroupOption;
    }

    public function setPropertyGroupOption(PropertyGroupOptionEntity $propertyGroupOption): void
    {
        $this->propertyGroupOption = $propertyGroupOption;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }
}
