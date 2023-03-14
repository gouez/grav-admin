<?php declare(strict_types=1);

namespace Laser\Core\Content\LandingPage\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('content')]
class LandingPageNotFoundException extends LaserHttpException
{
    public function __construct(string $landingPageId)
    {
        parent::__construct(
            'Landing page "{{ landingPageId }}" not found.',
            ['landingPageId' => $landingPageId]
        );
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__LANDING_PAGE_NOT_FOUND';
    }
}
