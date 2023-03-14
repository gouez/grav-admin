<?php declare(strict_types=1);

namespace Laser\Core\Content\ImportExport\Processing\Writer;

use Laser\Core\Content\ImportExport\Struct\Config;
use Laser\Core\Framework\Log\Package;

#[Package('system-settings')]
abstract class AbstractWriter
{
    abstract public function append(Config $config, array $data, int $index): void;

    abstract public function flush(Config $config, string $targetPath): void;

    abstract public function finish(Config $config, string $targetPath): void;

    protected function getDecorated(): AbstractWriter
    {
        throw new \RuntimeException('Implement getDecorated');
    }
}
