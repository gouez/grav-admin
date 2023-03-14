<?php declare(strict_types=1);

namespace Laser\Core\Content\ContactForm\Event;

use Laser\Core\Content\Flow\Dispatching\Aware\ContactFormDataAware;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Event\EventData\EventDataCollection;
use Laser\Core\Framework\Event\EventData\MailRecipientStruct;
use Laser\Core\Framework\Event\EventData\ObjectType;
use Laser\Core\Framework\Event\MailAware;
use Laser\Core\Framework\Event\SalesChannelAware;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Validation\DataBag\DataBag;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('content')]
final class ContactFormEvent extends Event implements SalesChannelAware, MailAware, ContactFormDataAware
{
    public const EVENT_NAME = 'contact_form.send';

    /**
     * @var array<int|string, mixed>
     */
    private readonly array $contactFormData;

    public function __construct(
        private readonly Context $context,
        private readonly string $salesChannelId,
        private readonly MailRecipientStruct $recipients,
        DataBag $contactFormData
    ) {
        $this->contactFormData = $contactFormData->all();
    }

    public static function getAvailableData(): EventDataCollection
    {
        return (new EventDataCollection())
            ->add('contactFormData', new ObjectType());
    }

    public function getName(): string
    {
        return self::EVENT_NAME;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function getMailStruct(): MailRecipientStruct
    {
        return $this->recipients;
    }

    public function getSalesChannelId(): string
    {
        return $this->salesChannelId;
    }

    /**
     * @return array<int|string, mixed>
     */
    public function getContactFormData(): array
    {
        return $this->contactFormData;
    }
}
