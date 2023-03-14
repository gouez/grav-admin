<?php declare(strict_types=1);

namespace Laser\Core\Content\Newsletter\Event\Subscriber;

use Laser\Core\Content\Newsletter\DataAbstractionLayer\NewsletterRecipientIndexingMessage;
use Laser\Core\Content\Newsletter\NewsletterEvents;
use Laser\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent;
use Laser\Core\Framework\Log\Package;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @internal
 */
#[Package('customer-order')]
class NewsletterRecipientDeletedSubscriber implements EventSubscriberInterface
{
    /**
     * @internal
     */
    public function __construct(private readonly MessageBusInterface $messageBus)
    {
    }

    /**
     * @return array<string, string|array{0: string, 1: int}|list<array{0: string, 1?: int}>>
     */
    public static function getSubscribedEvents(): array
    {
        return [NewsletterEvents::NEWSLETTER_RECIPIENT_DELETED_EVENT => 'onNewsletterRecipientDeleted'];
    }

    public function onNewsletterRecipientDeleted(EntityDeletedEvent $event): void
    {
        $message = new NewsletterRecipientIndexingMessage($event->getIds(), null, $event->getContext());
        $message->setDeletedNewsletterRecipients(true);

        $this->messageBus->dispatch($message);
    }
}
