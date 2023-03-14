<?php declare(strict_types=1);

namespace Laser\Core\System\StateMachine\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('checkout')]
class StateMachineInvalidEntityIdException extends LaserHttpException
{
    public function __construct(
        string $entityName,
        string $entityId
    ) {
        parent::__construct(
            'Unable to read entity "{{ entityName }}" with id "{{ entityId }}".',
            [
                'entityName' => $entityName,
                'entityId' => $entityId,
            ]
        );
    }

    public function getErrorCode(): string
    {
        return 'SYSTEM__STATE_MACHINE_INVALID_ENTITY_ID';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
