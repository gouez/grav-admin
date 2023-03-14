<?php declare(strict_types=1);

namespace Laser\Core\Content\Newsletter\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Laser\Core\System\SalesChannel\SalesChannelEntity;

#[Package('customer-order')]
class SalesChannelDomainNotFoundException extends LaserHttpException
{
    public function __construct(SalesChannelEntity $salesChannel)
    {
        parent::__construct(
            'No domain found for sales channel {{ salesChannel }}',
            ['salesChannel' => $salesChannel->getTranslation('name')]
        );
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__SALES_CHANNEL_DOMAIN_NOT_FOUND';
    }
}
