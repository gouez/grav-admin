<?php declare(strict_types=1);

namespace Laser\Core\Framework\Api\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('core')]
class ExpectationFailedException extends LaserHttpException
{
    /**
     * @param array<string> $fails
     */
    public function __construct(private readonly array $fails)
    {
        parent::__construct('API Expectations failed', []);
    }

    /**
     * @return array<string> $failedExpectations
     */
    public function getParameters(): array
    {
        return $this->fails;
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__API_EXPECTATION_FAILED';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_EXPECTATION_FAILED;
    }
}
