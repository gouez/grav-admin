<?php declare(strict_types=1);

namespace Laser\Core\DevOps\Docs\Script;

use Laser\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
interface ScriptReferenceGenerator
{
    /**
     * @return array<string, string>
     */
    public function generate(): array;
}
