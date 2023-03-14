<?php declare(strict_types=1);

namespace Laser\Core\Content\ImportExport\Processing\Reader;

use Laser\Core\Content\ImportExport\Aggregate\ImportExportLog\ImportExportLogEntity;
use Laser\Core\Framework\Log\Package;

#[Package('system-settings')]
abstract class AbstractReaderFactory
{
    abstract public function create(ImportExportLogEntity $logEntity): AbstractReader;

    abstract public function supports(ImportExportLogEntity $logEntity): bool;
}
