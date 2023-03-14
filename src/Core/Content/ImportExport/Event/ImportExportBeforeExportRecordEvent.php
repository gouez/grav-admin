<?php declare(strict_types=1);

namespace Laser\Core\Content\ImportExport\Event;

use Laser\Core\Content\ImportExport\Struct\Config;
use Laser\Core\Framework\Log\Package;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('system-settings')]
class ImportExportBeforeExportRecordEvent extends Event
{
    public function __construct(
        private readonly Config $config,
        private array $record,
        private readonly array $originalRecord
    ) {
    }

    public function getRecord(): array
    {
        return $this->record;
    }

    public function setRecord(array $record): void
    {
        $this->record = $record;
    }

    public function getOriginalRecord(): array
    {
        return $this->originalRecord;
    }

    public function getConfig(): Config
    {
        return $this->config;
    }
}
