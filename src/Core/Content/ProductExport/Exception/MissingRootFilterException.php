<?php declare(strict_types=1);

namespace Laser\Core\Content\ProductExport\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;

#[Package('sales-channel')]
class MissingRootFilterException extends LaserHttpException
{
    public function __construct()
    {
        parent::__construct('Missing root filter ');
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__PRODUCT_EXPORT_EMPTY';
    }
}
