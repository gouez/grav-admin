<?php declare(strict_types=1);

namespace Laser\Core\Content\ImportExport\Processing\Pipe;

use Laser\Core\Content\ImportExport\Aggregate\ImportExportLog\ImportExportLogEntity;
use Laser\Core\Framework\Log\Package;

#[Package('system-settings')]
abstract class AbstractPipeFactory
{
    abstract public function create(ImportExportLogEntity $logEntity): AbstractPipe;

    abstract public function supports(ImportExportLogEntity $logEntity): bool;
}
