<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Promotion\Cart\Discount\Filter\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('checkout')]
class FilterSorterNotFoundException extends LaserHttpException
{
    public function __construct(string $key)
    {
        parent::__construct('Sorter "{{ key }}" has not been found!', ['key' => $key]);
    }

    public function getErrorCode(): string
    {
        return 'CHECKOUT__FILTER_SORTER_NOT_FOUND';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
