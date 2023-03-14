<?php declare(strict_types=1);

namespace Laser\Core\DevOps\Environment;

use Laser\Core\Framework\Log\Package;

#[Package('core')]
interface EnvironmentHelperTransformerInterface
{
    public static function transform(EnvironmentHelperTransformerData $data): void;
}
