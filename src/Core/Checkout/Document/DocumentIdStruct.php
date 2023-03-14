<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Document;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\Struct;

#[Package('customer-order')]
class DocumentIdStruct extends Struct
{
    public function __construct(
        protected string $id,
        protected string $deepLinkCode,
        protected ?string $mediaId = null
    ) {
    }

    public function getDeepLinkCode(): string
    {
        return $this->deepLinkCode;
    }

    public function setDeepLinkCode(string $deepLinkCode): void
    {
        $this->deepLinkCode = $deepLinkCode;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getMediaId(): ?string
    {
        return $this->mediaId;
    }

    public function getApiAlias(): string
    {
        return 'document_id';
    }
}
