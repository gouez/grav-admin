<?php declare(strict_types=1);

namespace Laser\Core\System\SalesChannel\Context;

use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\BaseContext;

/**
 * @internal
 */
#[Package('core')]
abstract class AbstractBaseContextFactory
{
    abstract public function getDecorated(): AbstractBaseContextFactory;

    /**
     * @param array<string, mixed> $options
     */
    abstract public function create(string $salesChannelId, array $options = []): BaseContext;
}
