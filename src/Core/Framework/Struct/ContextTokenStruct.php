<?php declare(strict_types=1);

namespace Laser\Core\Framework\Struct;

use Laser\Core\Framework\Log\Package;
use Laser\Core\PlatformRequest;

#[Package('core')]
class ContextTokenStruct extends Struct
{
    /**
     * @var string
     */
    protected $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize();

        unset($data['token']);

        $data[PlatformRequest::HEADER_CONTEXT_TOKEN] = $this->getToken();

        return $data;
    }

    public function getApiAlias(): string
    {
        return 'context_token';
    }
}
