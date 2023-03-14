<?php declare(strict_types=1);

namespace Laser\Core\Framework\Routing\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('core')]
class InvalidRequestParameterException extends LaserHttpException
{
    public function __construct(string $name)
    {
        parent::__construct(
            'The parameter "{{ parameter }}" is invalid.',
            ['parameter' => $name]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__INVALID_REQUEST_PARAMETER';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
