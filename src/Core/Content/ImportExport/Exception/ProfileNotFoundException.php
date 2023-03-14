<?php declare(strict_types=1);

namespace Laser\Core\Content\ImportExport\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('system-settings')]
class ProfileNotFoundException extends LaserHttpException
{
    public function __construct(string $profileId)
    {
        parent::__construct('Cannot find import/export profile with id {{ profileId }}', ['profileId' => $profileId]);
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__IMPORT_EXPORT_PROFILE_NOT_FOUND';
    }
}
