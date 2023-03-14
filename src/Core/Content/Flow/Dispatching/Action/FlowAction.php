<?php declare(strict_types=1);

namespace Laser\Core\Content\Flow\Dispatching\Action;

use Laser\Core\Content\Flow\Dispatching\StorableFlow;
use Laser\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('business-ops')]
abstract class FlowAction
{
    /**
     * @return array<int, string>
     */
    abstract public function requirements(): array;

    abstract public function handleFlow(StorableFlow $flow): void;

    abstract public static function getName(): string;
}
