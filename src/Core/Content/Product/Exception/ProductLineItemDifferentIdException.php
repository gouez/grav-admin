<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('inventory')]
class ProductLineItemDifferentIdException extends LaserHttpException
{
    public function __construct(string $lineItemId)
    {
        $message = sprintf('The `productId` and `referencedId` of the line item %s are not identical.', $lineItemId);
        parent::__construct($message);
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__PRODUCT_REFERENCED_ID_DIFFERENT';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
