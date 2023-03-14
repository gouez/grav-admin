<?php declare(strict_types=1);

namespace Laser\Core\System\StateMachine\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('checkout')]
class UnnecessaryTransitionException extends LaserHttpException
{
    public function __construct(string $transition)
    {
        parent::__construct(
            'The transition "{{ transition }}" is unnecessary, already on desired state.',
            [
                'transition' => $transition,
            ]
        );
    }

    public function getErrorCode(): string
    {
        return 'SYSTEM__UNNECESSARY_TRANSITION';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
