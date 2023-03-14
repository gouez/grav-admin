<?php declare(strict_types=1);

namespace Laser\Core\Content\ImportExport;

use Laser\Core\Framework\DataAbstractionLayer\EntityCollection;
use Laser\Core\Framework\Log\Package;

/**
 * @extends EntityCollection<ImportExportProfileTranslationEntity>
 */
#[Package('system-settings')]
class ImportExportProfileTranslationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ImportExportProfileTranslationEntity::class;
    }
}
