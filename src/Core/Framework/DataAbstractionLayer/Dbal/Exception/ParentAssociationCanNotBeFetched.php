<?php declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer\Dbal\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('core')]
class ParentAssociationCanNotBeFetched extends LaserHttpException
{
    public function __construct()
    {
        parent::__construct(
            'It is not possible to read the parent association directly. Please read the parents via a separate call over the repository'
        );
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__PARENT_ASSOCIATION_CAN_NOT_BE_FETCHED';
    }
}
