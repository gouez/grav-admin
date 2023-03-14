<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Payment\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('checkout')]
class PluginPaymentMethodsDeleteRestrictionException extends LaserHttpException
{
    public function __construct(?\Throwable $e = null)
    {
        parent::__construct('Plugin payment methods can not be deleted via API.', [], $e);
    }

    public function getErrorCode(): string
    {
        return 'CHECKOUT__PLUGIN_PAYMENT_METHOD_DELETE_RESTRICTION';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
