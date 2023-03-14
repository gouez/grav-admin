<?php declare(strict_types=1);

namespace Laser\Core\Content\ProductExport\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;

#[Package('sales-channel')]
class SalesChannelDomainNotFoundException extends LaserHttpException
{
    public function __construct(string $id)
    {
        parent::__construct('Sales channel domain with ID {{ id }} not found', ['id' => $id]);
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__PRODUCT_EXPORT_SALES_CHANNEL_DOMAIN_NOT_FOUND';
    }
}
