<?php declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer\Dbal\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('core')]
class InvalidSortingDirectionException extends LaserHttpException
{
    public function __construct(string $direction)
    {
        parent::__construct(
            'The given sort direction "{{ direction }}" is invalid.',
            ['direction' => $direction]
        );
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__INVALID_SORT_DIRECTION';
    }
}
