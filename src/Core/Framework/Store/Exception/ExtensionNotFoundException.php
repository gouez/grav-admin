<?php declare(strict_types=1);

namespace Laser\Core\Framework\Store\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('merchant-services')]
class ExtensionNotFoundException extends LaserHttpException
{
    public static function fromTechnicalName(string $technicalName): self
    {
        return new self(
            'Could not find extension with technical name "{{technicalName}}".',
            ['technicalName' => $technicalName]
        );
    }

    public static function fromId(string $id): self
    {
        return new self(
            'Could not find extension with id "{{id}}".',
            ['id' => $id]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__EXTENSION_NOT_FOUND';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
