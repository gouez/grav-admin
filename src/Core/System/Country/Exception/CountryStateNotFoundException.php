<?php declare(strict_types=1);

namespace Laser\Core\System\Country\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('system-settings')]
class CountryStateNotFoundException extends LaserHttpException
{
    public function __construct(string $id)
    {
        parent::__construct(
            'Country state with id "{{ stateId }}" not found.',
            ['stateId' => $id]
        );
    }

    public function getErrorCode(): string
    {
        return 'CHECKOUT__COUNTRY_STATE_NOT_FOUND';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
