<?php declare(strict_types=1);

namespace Laser\Core\Framework\Increment\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('core')]
class IncrementGatewayNotFoundException extends LaserHttpException
{
    public function __construct(string $pool)
    {
        parent::__construct(
            'Increment gateway for pool "{{ pool }}" was not found.',
            ['pool' => $pool]
        );
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__INCREMENT_GATEWAY_NOT_FOUND';
    }
}
