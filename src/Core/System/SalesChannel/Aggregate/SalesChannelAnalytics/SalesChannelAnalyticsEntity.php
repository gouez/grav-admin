<?php declare(strict_types=1);

namespace Laser\Core\System\SalesChannel\Aggregate\SalesChannelAnalytics;

use Laser\Core\Framework\DataAbstractionLayer\Entity;
use Laser\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelEntity;

#[Package('sales-channel')]
class SalesChannelAnalyticsEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $trackingId;

    /**
     * @var bool
     */
    protected $active;

    /**
     * @var bool
     */
    protected $trackOrders;

    /**
     * @var bool
     */
    protected $anonymizeIp;

    /**
     * @var SalesChannelEntity
     */
    protected $salesChannel;

    public function getTrackingId(): string
    {
        return $this->trackingId;
    }

    public function setTrackingId(string $trackingId): void
    {
        $this->trackingId = $trackingId;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function isTrackOrders(): bool
    {
        return $this->trackOrders;
    }

    public function setTrackOrders(bool $trackOrders): void
    {
        $this->trackOrders = $trackOrders;
    }

    public function isAnonymizeIp(): bool
    {
        return $this->anonymizeIp;
    }

    public function setAnonymizeIp(bool $anonymizeIp): void
    {
        $this->anonymizeIp = $anonymizeIp;
    }

    public function getSalesChannel(): SalesChannelEntity
    {
        return $this->salesChannel;
    }

    public function setSalesChannel(SalesChannelEntity $salesChannel): void
    {
        $this->salesChannel = $salesChannel;
    }
}
