<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Promotion\Exception;

use Laser\Core\Checkout\Cart\LineItem\LineItem;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('checkout')]
class PriceNotFoundException extends LaserHttpException
{
    public function __construct(LineItem $item)
    {
        parent::__construct('No calculated price found for item ' . $item->getId());
    }

    public function getErrorCode(): string
    {
        return 'CHECKOUT__PRICE_NOT_FOUND_FOR_ITEM';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
