<?php declare(strict_types=1);

namespace Laser\Core\Content\Newsletter\Event;

use Laser\Core\Content\Flow\Dispatching\Aware\NewsletterRecipientAware;
use Laser\Core\Content\Flow\Dispatching\Aware\UrlAware;
use Laser\Core\Content\Newsletter\Aggregate\NewsletterRecipient\NewsletterRecipientDefinition;
use Laser\Core\Content\Newsletter\Aggregate\NewsletterRecipient\NewsletterRecipientEntity;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Event\EventData\EntityType;
use Laser\Core\Framework\Event\EventData\EventDataCollection;
use Laser\Core\Framework\Event\EventData\MailRecipientStruct;
use Laser\Core\Framework\Event\EventData\ScalarValueType;
use Laser\Core\Framework\Event\MailAware;
use Laser\Core\Framework\Event\SalesChannelAware;
use Laser\Core\Framework\Log\Package;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('customer-order')]
class NewsletterRegisterEvent extends Event implements SalesChannelAware, MailAware, NewsletterRecipientAware, UrlAware
{
    final public const EVENT_NAME = 'newsletter.register';

    private ?MailRecipientStruct $mailRecipientStruct = null;

    public function __construct(
        private readonly Context $context,
        private readonly NewsletterRecipientEntity $newsletterRecipient,
        private readonly string $url,
        private readonly string $salesChannelId
    ) {
    }

    public static function getAvailableData(): EventDataCollection
    {
        return (new EventDataCollection())
            ->add('newsletterRecipient', new EntityType(NewsletterRecipientDefinition::class))
            ->add('url', new ScalarValueType(ScalarValueType::TYPE_STRING));
    }

    public function getName(): string
    {
        return self::EVENT_NAME;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function getNewsletterRecipient(): NewsletterRecipientEntity
    {
        return $this->newsletterRecipient;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getMailStruct(): MailRecipientStruct
    {
        if (!$this->mailRecipientStruct) {
            $recipientName = $this->newsletterRecipient->getEmail();

            if ($this->newsletterRecipient->getFirstName() && $this->newsletterRecipient->getLastName()) {
                $recipientName = $this->newsletterRecipient->getFirstName() . ' ' . $this->newsletterRecipient->getLastName();
            }

            $this->mailRecipientStruct = new MailRecipientStruct(
                [
                    $this->newsletterRecipient->getEmail() => $recipientName,
                ]
            );
        }

        return $this->mailRecipientStruct;
    }

    public function getSalesChannelId(): string
    {
        return $this->salesChannelId;
    }

    public function getNewsletterRecipientId(): string
    {
        return $this->newsletterRecipient->getId();
    }
}
