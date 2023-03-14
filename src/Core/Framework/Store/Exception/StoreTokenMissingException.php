<?php declare(strict_types=1);

namespace Laser\Core\Framework\Store\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('merchant-services')]
class StoreTokenMissingException extends LaserHttpException
{
    public function __construct()
    {
        parent::__construct('Store token is missing');
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__STORE_TOKEN_IS_MISSING';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_FORBIDDEN;
    }
}
