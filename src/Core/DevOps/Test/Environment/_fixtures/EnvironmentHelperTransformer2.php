<?php declare(strict_types=1);

namespace Laser\Core\DevOps\Test\Environment\_fixtures;

use Laser\Core\DevOps\Environment\EnvironmentHelperTransformerData;
use Laser\Core\DevOps\Environment\EnvironmentHelperTransformerInterface;
use Laser\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
class EnvironmentHelperTransformer2 implements EnvironmentHelperTransformerInterface
{
    public static function transform(EnvironmentHelperTransformerData $data): void
    {
        $data->setValue($data->getValue() !== null ? $data->getValue() . ' baz' : null);
    }
}
