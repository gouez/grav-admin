<?php declare(strict_types=1);

namespace Laser\Core\System\SalesChannel\Event;

use Laser\Core\Framework\Context;
use Laser\Core\Framework\Event\LaserSalesChannelEvent;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('core')]
class SalesChannelContextTokenChangeEvent extends Event implements LaserSalesChannelEvent
{
    /**
     * @var SalesChannelContext
     */
    protected $salesChannelContext;

    /**
     * @var string
     */
    protected $previousToken;

    /**
     * @var string
     */
    protected $currentToken;

    public function __construct(
        SalesChannelContext $salesChannelContext,
        string $previousToken,
        string $currentToken
    ) {
        $this->salesChannelContext = $salesChannelContext;
        $this->previousToken = $previousToken;
        $this->currentToken = $currentToken;
    }

    public function getSalesChannelContext(): SalesChannelContext
    {
        return $this->salesChannelContext;
    }

    public function getContext(): Context
    {
        return $this->salesChannelContext->getContext();
    }

    public function getPreviousToken(): string
    {
        return $this->previousToken;
    }

    public function getCurrentToken(): string
    {
        return $this->currentToken;
    }
}
