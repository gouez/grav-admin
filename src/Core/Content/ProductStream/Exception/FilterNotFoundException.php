<?php declare(strict_types=1);

namespace Laser\Core\Content\ProductStream\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;

#[Package('business-ops')]
class FilterNotFoundException extends LaserHttpException
{
    public function __construct(string $type)
    {
        parent::__construct('Filter for type {{ type}} not found', ['type' => $type]);
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__PRODUCT_STREAM_FILTER_NOT_FOUND';
    }
}
