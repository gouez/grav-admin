<?php declare(strict_types=1);

namespace Laser\Core\Content\Newsletter\DataAbstractionLayer;

use Laser\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexingMessage;
use Laser\Core\Framework\Log\Package;

#[Package('customer-order')]
class NewsletterRecipientIndexingMessage extends EntityIndexingMessage
{
    private bool $deletedNewsletterRecipients = false;

    public function isDeletedNewsletterRecipients(): bool
    {
        return $this->deletedNewsletterRecipients;
    }

    public function setDeletedNewsletterRecipients(bool $deletedNewsletterRecipients): void
    {
        $this->deletedNewsletterRecipients = $deletedNewsletterRecipients;
    }
}
