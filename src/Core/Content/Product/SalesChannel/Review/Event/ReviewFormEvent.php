<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\SalesChannel\Review\Event;

use Laser\Core\Content\Flow\Dispatching\Aware\ReviewFormDataAware;
use Laser\Core\Content\Product\ProductDefinition;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Event\CustomerAware;
use Laser\Core\Framework\Event\EventData\EntityType;
use Laser\Core\Framework\Event\EventData\EventDataCollection;
use Laser\Core\Framework\Event\EventData\MailRecipientStruct;
use Laser\Core\Framework\Event\EventData\ObjectType;
use Laser\Core\Framework\Event\MailAware;
use Laser\Core\Framework\Event\ProductAware;
use Laser\Core\Framework\Event\SalesChannelAware;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Validation\DataBag\DataBag;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('content')]
final class ReviewFormEvent extends Event implements SalesChannelAware, MailAware, ReviewFormDataAware, ProductAware, CustomerAware
{
    public const EVENT_NAME = 'review_form.send';

    /**
     * @var array<int|string, mixed>
     */
    private readonly array $reviewFormData;

    public function __construct(
        private readonly Context $context,
        private readonly string $salesChannelId,
        private readonly MailRecipientStruct $recipients,
        DataBag $reviewFormData,
        private readonly string $productId,
        private readonly string $customerId
    ) {
        $this->reviewFormData = $reviewFormData->all();
    }

    public static function getAvailableData(): EventDataCollection
    {
        return (new EventDataCollection())
            ->add(ReviewFormDataAware::REVIEW_FORM_DATA, new ObjectType())
            ->add(ProductAware::PRODUCT, new EntityType(ProductDefinition::class));
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
    public function getReviewFormData(): array
    {
        return $this->reviewFormData;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function getCustomerId(): string
    {
        return $this->customerId;
    }
}
