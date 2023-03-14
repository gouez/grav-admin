<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('inventory')]
class DuplicateProductSearchConfigFieldException extends LaserHttpException
{
    public function __construct(
        string $fieldName,
        \Throwable $e
    ) {
        parent::__construct(
            'Product search config with field {{ fieldName }} already exists.',
            ['fieldName' => $fieldName],
            $e
        );
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__DUPLICATE_PRODUCT_SEARCH_CONFIG_FIELD';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
