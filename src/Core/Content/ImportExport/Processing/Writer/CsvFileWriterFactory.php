<?php declare(strict_types=1);

namespace Laser\Core\Content\ImportExport\Processing\Writer;

use League\Flysystem\FilesystemOperator;
use Laser\Core\Content\ImportExport\Aggregate\ImportExportLog\ImportExportLogEntity;
use Laser\Core\Framework\Log\Package;

#[Package('system-settings')]
class CsvFileWriterFactory extends AbstractWriterFactory
{
    /**
     * @internal
     */
    public function __construct(private readonly FilesystemOperator $filesystem)
    {
    }

    public function create(ImportExportLogEntity $logEntity): AbstractWriter
    {
        return new CsvFileWriter($this->filesystem);
    }

    public function supports(ImportExportLogEntity $logEntity): bool
    {
        return $logEntity->getProfile()->getFileType() === 'text/csv';
    }
}
