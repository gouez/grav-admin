<?php declare(strict_types=1);

namespace Laser\Core\System\Salutation\Aggregate\SalutationTranslation;

use Laser\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use Laser\Core\Framework\DataAbstractionLayer\TranslationEntity;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\Salutation\SalutationEntity;

#[Package('customer-order')]
class SalutationTranslationEntity extends TranslationEntity
{
    use EntityCustomFieldsTrait;

    /**
     * @var string
     */
    protected $salutationId;

    /**
     * @var string|null
     */
    protected $displayName;

    /**
     * @var string|null
     */
    protected $letterName;

    /**
     * @var SalutationEntity|null
     */
    protected $salutation;

    public function getSalutationId(): string
    {
        return $this->salutationId;
    }

    public function setSalutationId(string $salutationId): void
    {
        $this->salutationId = $salutationId;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(?string $displayName): void
    {
        $this->displayName = $displayName;
    }

    public function getLetterName(): ?string
    {
        return $this->letterName;
    }

    public function setLetterName(?string $letterName): void
    {
        $this->letterName = $letterName;
    }

    public function getSalutation(): ?SalutationEntity
    {
        return $this->salutation;
    }

    public function setSalutation(?SalutationEntity $salutation): void
    {
        $this->salutation = $salutation;
    }
}
