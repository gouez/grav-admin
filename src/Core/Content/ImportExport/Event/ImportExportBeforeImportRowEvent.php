<?php declare(strict_types=1);

namespace Laser\Core\Content\ImportExport\Event;

use Laser\Core\Content\ImportExport\Struct\Config;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Log\Package;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('system-settings')]
class ImportExportBeforeImportRowEvent extends Event
{
    public function __construct(
        private array $row,
        private readonly Config $config,
        private readonly Context $context
    ) {
    }

    public function getRow(): array
    {
        return $this->row;
    }

    public function setRow(array $row): void
    {
        $this->row = $row;
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    public function getContext(): Context
    {
        return $this->context;
    }
}
