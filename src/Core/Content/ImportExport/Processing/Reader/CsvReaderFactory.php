<?php declare(strict_types=1);

namespace Laser\Core\Content\ImportExport\Processing\Reader;

use Laser\Core\Content\ImportExport\Aggregate\ImportExportLog\ImportExportLogEntity;
use Laser\Core\Framework\Log\Package;

#[Package('system-settings')]
class CsvReaderFactory extends AbstractReaderFactory
{
    public function create(ImportExportLogEntity $logEntity): AbstractReader
    {
        return new CsvReader();
    }

    public function supports(ImportExportLogEntity $logEntity): bool
    {
        return $logEntity->getProfile()->getFileType() === 'text/csv';
    }
}
