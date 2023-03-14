<?php declare(strict_types=1);

namespace Laser\Core\Framework\Routing;

use Laser\Core\Checkout\Payment\Controller\PaymentController;
use Laser\Core\Framework\Log\Package;

#[Package('core')]
class PaymentScopeWhitelist implements RouteScopeWhitelistInterface
{
    public function applies(string $controllerClass): bool
    {
        return $controllerClass === PaymentController::class;
    }
}
