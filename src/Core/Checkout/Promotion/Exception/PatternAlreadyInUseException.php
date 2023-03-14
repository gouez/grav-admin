<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Promotion\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('checkout')]
class PatternAlreadyInUseException extends LaserHttpException
{
    final public const ERROR_CODE = 'PROMOTION__INDIVIDUAL_CODES_PATTERN_ALREADY_IN_USE';

    public function __construct()
    {
        parent::__construct(
            'Code pattern already exists in another promotion. Please provide a different pattern.'
        );
    }

    public function getErrorCode(): string
    {
        return self::ERROR_CODE;
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
