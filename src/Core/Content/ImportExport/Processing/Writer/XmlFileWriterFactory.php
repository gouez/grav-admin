<?php declare(strict_types=1);

namespace Laser\Core\Content\ImportExport\Processing\Writer;

use League\Flysystem\FilesystemOperator;
use Laser\Core\Content\ImportExport\Aggregate\ImportExportLog\ImportExportLogEntity;
use Laser\Core\Framework\Log\Package;

#[Package('system-settings')]
class XmlFileWriterFactory extends AbstractWriterFactory
{
    public function __construct(private readonly FilesystemOperator $filesystem)
    {
    }

    public function create(ImportExportLogEntity $logEntity): AbstractWriter
    {
        return new XmlFileWriter($this->filesystem);
    }

    public function supports(ImportExportLogEntity $logEntity): bool
    {
        return $logEntity->getActivity() === ImportExportLogEntity::ACTIVITY_EXPORT
            && $logEntity->getProfile()->getFileType() === 'text/xml';
    }
}
