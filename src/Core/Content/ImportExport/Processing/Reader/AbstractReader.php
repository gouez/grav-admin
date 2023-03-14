<?php declare(strict_types=1);

namespace Laser\Core\Content\ImportExport\Processing\Reader;

use Laser\Core\Content\ImportExport\Struct\Config;
use Laser\Core\Framework\Log\Package;

#[Package('system-settings')]
abstract class AbstractReader
{
    /**
     * @param resource $resource
     */
    abstract public function read(Config $config, $resource, int $offset): iterable;

    abstract public function getOffset(): int;

    protected function getDecorated(): AbstractReader
    {
        throw new \RuntimeException('Implement getDecorated');
    }
}
