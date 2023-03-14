<?php declare(strict_types=1);

namespace Laser\Core\Content\MailTemplate\Subscriber;

use Laser\Core\Content\MailTemplate\MailTemplateActions;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\Struct;

#[Package('sales-channel')]
class MailSendSubscriberConfig extends Struct
{
    final public const ACTION_NAME = MailTemplateActions::MAIL_TEMPLATE_MAIL_SEND_ACTION;
    final public const MAIL_CONFIG_EXTENSION = 'mail-attachments';

    /**
     * @var bool
     */
    protected $skip;

    /**
     * @var array<string>
     */
    protected $documentIds = [];

    /**
     * @var array<string>
     */
    protected $mediaIds = [];

    /**
     * @param array<string> $documentIds
     * @param array<string> $mediaIds
     */
    public function __construct(
        bool $skip,
        array $documentIds = [],
        array $mediaIds = []
    ) {
        $this->skip = $skip;
        $this->documentIds = $documentIds;
        $this->mediaIds = $mediaIds;
    }

    public function skip(): bool
    {
        return $this->skip;
    }

    public function setSkip(bool $skip): void
    {
        $this->skip = $skip;
    }

    public function getDocumentIds(): array
    {
        return $this->documentIds;
    }

    public function getMediaIds(): array
    {
        return $this->mediaIds;
    }

    /**
     * @param array<string> $documentIds
     */
    public function setDocumentIds(array $documentIds): void
    {
        $this->documentIds = $documentIds;
    }

    /**
     * @param array<string> $mediaIds
     */
    public function setMediaIds(array $mediaIds): void
    {
        $this->mediaIds = $mediaIds;
    }
}
