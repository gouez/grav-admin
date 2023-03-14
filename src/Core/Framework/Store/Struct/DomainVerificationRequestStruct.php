<?php declare(strict_types=1);

namespace Laser\Core\Framework\Store\Struct;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\Struct;

/**
 * @codeCoverageIgnore
 */
#[Package('merchant-services')]
class DomainVerificationRequestStruct extends Struct
{
    /**
     * @var string
     */
    protected $content;

    /**
     * @var string
     */
    protected $fileName;

    public function __construct(
        string $content,
        string $filename
    ) {
        $this->content = $content;
        $this->fileName = $filename;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getApiAlias(): string
    {
        return 'store_domain_verification_request';
    }
}
