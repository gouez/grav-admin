<?php declare(strict_types=1);

namespace Laser\Core\System\Snippet\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('system-settings')]
class FilterNotFoundException extends LaserHttpException
{
    public function __construct(
        string $filterName,
        string $class
    ) {
        parent::__construct(
            'The filter "{{ filter }}" was not found in "{{ class }}".',
            ['filter' => $filterName, 'class' => $class]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__FILTER_NOT_FOUND';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
