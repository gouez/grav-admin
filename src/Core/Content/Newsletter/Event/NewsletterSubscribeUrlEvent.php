<?php declare(strict_types=1);

namespace Laser\Core\Content\Newsletter\Event;

use Laser\Core\Content\Newsletter\Aggregate\NewsletterRecipient\NewsletterRecipientEntity;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Event\LaserSalesChannelEvent;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('customer-order')]
class NewsletterSubscribeUrlEvent extends Event implements LaserSalesChannelEvent
{
    public function __construct(
        private readonly SalesChannelContext $salesChannelContext,
        private string $subscribeUrl,
        private readonly string $hashedEmail,
        private readonly string $hash,
        private readonly array $data,
        private readonly NewsletterRecipientEntity $recipient
    ) {
    }

    public function getSubscribeUrl(): string
    {
        return $this->subscribeUrl;
    }

    public function setSubscribeUrl(string $subscribeUrl): void
    {
        $this->subscribeUrl = $subscribeUrl;
    }

    public function getSalesChannelContext(): SalesChannelContext
    {
        return $this->salesChannelContext;
    }

    public function getContext(): Context
    {
        return $this->salesChannelContext->getContext();
    }

    public function getSalesChannelId(): string
    {
        return $this->salesChannelContext->getSalesChannel()->getId();
    }

    public function getHashedEmail(): string
    {
        return $this->hashedEmail;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getRecipient(): NewsletterRecipientEntity
    {
        return $this->recipient;
    }
}
