<?php declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer\Dbal\FieldResolver;

use Laser\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
abstract class AbstractFieldResolver
{
    abstract public function join(FieldResolverContext $context): string;
}
