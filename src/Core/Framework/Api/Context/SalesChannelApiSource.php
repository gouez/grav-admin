<?php declare(strict_types=1);

namespace Laser\Core\Framework\Api\Context;

use Laser\Core\Framework\Log\Package;

#[Package('core')]
class SalesChannelApiSource implements ContextSource
{
    public string $type = 'sales-channel';

    public function __construct(private readonly string $salesChannelId)
    {
    }

    public function getSalesChannelId(): string
    {
        return $this->salesChannelId;
    }
}
