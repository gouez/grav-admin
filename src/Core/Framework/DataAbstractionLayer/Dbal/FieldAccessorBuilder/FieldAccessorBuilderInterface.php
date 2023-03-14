<?php declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer\Dbal\FieldAccessorBuilder;

use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\Field\Field;
use Laser\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
interface FieldAccessorBuilderInterface
{
    public function buildAccessor(string $root, Field $field, Context $context, string $accessor): ?string;
}
