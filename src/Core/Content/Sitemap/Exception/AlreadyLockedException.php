<?php declare(strict_types=1);

namespace Laser\Core\Content\Sitemap\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('sales-channel')]
class AlreadyLockedException extends LaserHttpException
{
    public function __construct(SalesChannelContext $salesChannelContext)
    {
        parent::__construct('Cannot acquire lock for sales channel {{salesChannelId}} and language {{languageId}}', [
            'salesChannelId' => $salesChannelContext->getSalesChannel()->getId(),
            'languageId' => $salesChannelContext->getLanguageId(),
        ]);
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__SITEMAP_ALREADY_LOCKED';
    }
}
