<?php declare(strict_types=1);

namespace Laser\Core\System\SystemConfig\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('system-settings')]
class InvalidKeyException extends LaserHttpException
{
    public function __construct(string $key)
    {
        parent::__construct('Invalid key \'{{ key }}\'', ['key' => $key]);
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function getErrorCode(): string
    {
        return 'SYSTEM__INVALID_KEY';
    }
}
