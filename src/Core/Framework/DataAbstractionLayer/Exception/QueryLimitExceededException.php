<?php declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('core')]
class QueryLimitExceededException extends LaserHttpException
{
    public function __construct(
        $maxLimit,
        $limit
    ) {
        parent::__construct(
            'The limit must be lower than or equal to MAX_LIMIT(={{ maxLimit }}). Given: {{ limit }}',
            ['maxLimit' => $maxLimit, 'limit' => $limit]
        );
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__QUERY_LIMIT_EXCEEDED';
    }
}
