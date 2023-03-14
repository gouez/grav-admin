<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('inventory')]
class VariantNotFoundException extends LaserHttpException
{
    public function __construct(
        string $productId,
        array $options
    ) {
        parent::__construct(
            'Variant for productId {{ productId }} with options {{ options }} not found.',
            [
                'productId' => $productId,
                'options' => json_encode($options, \JSON_THROW_ON_ERROR),
            ]
        );
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__PRODUCT_VARIANT_NOT_FOUND';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
