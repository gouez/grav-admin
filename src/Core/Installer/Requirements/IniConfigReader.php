<?php declare(strict_types=1);

namespace Laser\Core\Installer\Requirements;

use Laser\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core
Extracted to be able to mock all ini values')]
class IniConfigReader
{
    public function get(string $key): string
    {
        return (string) \ini_get($key);
    }
}
