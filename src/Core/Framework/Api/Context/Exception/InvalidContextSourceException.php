<?php declare(strict_types=1);

namespace Laser\Core\Framework\Api\Context\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;

#[Package('core')]
class InvalidContextSourceException extends LaserHttpException
{
    public function __construct(
        string $expected,
        string $actual
    ) {
        parent::__construct(
            'Expected ContextSource of "{{expected}}", but got "{{actual}}".',
            ['expected' => $expected, 'actual' => $actual]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__INVALID_CONTEXT_SOURCE';
    }
}
