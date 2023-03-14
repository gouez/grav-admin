<?php declare(strict_types=1);

namespace Laser\Core\System\SalesChannel\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('core')]
class ContextPermissionsLockedException extends LaserHttpException
{
    public function __construct()
    {
        parent::__construct('Context permission in SalesChannel context already locked.');
    }

    public function getErrorCode(): string
    {
        return 'CHECKOUT__CONTEXT_PERMISSIONS_LOCKED';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
