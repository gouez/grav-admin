<?php declare(strict_types=1);

namespace Laser\Core\Content\ImportExport\Processing\Pipe;

use Laser\Core\Content\ImportExport\Struct\Config;
use Laser\Core\Framework\Log\Package;

#[Package('system-settings')]
abstract class AbstractPipe
{
    abstract public function in(Config $config, iterable $record): iterable;

    abstract public function out(Config $config, iterable $record): iterable;

    protected function getDecorated(): AbstractPipe
    {
        throw new \RuntimeException('Implement getDecorated');
    }
}
