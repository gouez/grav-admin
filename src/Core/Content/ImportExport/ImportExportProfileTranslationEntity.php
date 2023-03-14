<?php declare(strict_types=1);

namespace Laser\Core\Content\ImportExport;

use Laser\Core\Framework\DataAbstractionLayer\TranslationEntity;
use Laser\Core\Framework\Log\Package;

#[Package('system-settings')]
class ImportExportProfileTranslationEntity extends TranslationEntity
{
    /**
     * @var string
     */
    protected $importExportProfileId;

    /**
     * @var string|null
     */
    protected $label;

    /**
     * @var ImportExportProfileEntity
     */
    protected $importExportProfile;

    public function getImportExportProfileId(): string
    {
        return $this->importExportProfileId;
    }

    public function setImportExportProfileId(string $importExportProfileId): void
    {
        $this->importExportProfileId = $importExportProfileId;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): void
    {
        $this->label = $label;
    }

    public function getImportExportProfile(): ImportExportProfileEntity
    {
        return $this->importExportProfile;
    }

    public function setImportExportProfile(ImportExportProfileEntity $importExportProfile): void
    {
        $this->importExportProfile = $importExportProfile;
    }
}
