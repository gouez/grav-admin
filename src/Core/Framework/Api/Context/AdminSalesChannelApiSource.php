<?php declare(strict_types=1);

namespace Laser\Core\Framework\Api\Context;

use Laser\Core\Framework\Context;
use Laser\Core\Framework\Log\Package;

#[Package('core')]
class AdminSalesChannelApiSource extends SalesChannelApiSource
{
    public string $type = 'admin-sales-channel-api';

    /**
     * @var Context
     */
    protected $originalContext;

    public function __construct(
        string $salesChannelId,
        Context $originalContext
    ) {
        parent::__construct($salesChannelId);

        $this->originalContext = $originalContext;
    }

    public function getOriginalContext(): Context
    {
        return $this->originalContext;
    }
}
