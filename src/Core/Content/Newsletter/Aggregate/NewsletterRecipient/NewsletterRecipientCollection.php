<?php declare(strict_types=1);

namespace Laser\Core\Content\Newsletter\Aggregate\NewsletterRecipient;

use Laser\Core\Framework\DataAbstractionLayer\EntityCollection;
use Laser\Core\Framework\Log\Package;

/**
 * @extends EntityCollection<NewsletterRecipientEntity>
 */
#[Package('customer-order')]
class NewsletterRecipientCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'newsletter_recipient_collection';
    }

    protected function getExpectedClass(): string
    {
        return NewsletterRecipientEntity::class;
    }
}
