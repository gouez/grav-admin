<?php declare(strict_types=1);

namespace Laser\Core\Framework\App\ActionButton\Response;

use Laser\Core\Framework\App\ActionButton\AppAction;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Log\Package;

/**
 * @internal only for use by the app-system
 */
#[Package('core')]
interface ActionButtonResponseFactoryInterface
{
    public function supports(string $actionType): bool;

    public function create(AppAction $action, array $payload, Context $context): ActionButtonResponse;
}
