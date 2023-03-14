<?php declare(strict_types=1);

namespace Laser\Core\System\SalesChannel;

use Laser\Core\Framework\Feature;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\ArrayStruct;
use Laser\Core\PlatformRequest;

#[Package('core')]
class ContextTokenResponse extends StoreApiResponse
{
    /**
     * @var ArrayStruct<string, mixed>
     */
    protected $object;

    public function __construct(
        string $token,
        ?string $redirectUrl = null
    ) {
        $object = [
            'contextToken' => $token,
            'redirectUrl' => $redirectUrl,
        ];

        // Since v6.6.0.0, using the token in response body is deprecated
        // Please fetch it from the header instead
        if (Feature::isActive('v6.6.0.0')) {
            unset($object['contextToken']);
        }

        parent::__construct(new ArrayStruct($object));

        $this->headers->set(PlatformRequest::HEADER_CONTEXT_TOKEN, $token);
    }

    public function getToken(): string
    {
        if (Feature::isActive('v6.6.0.0')) {
            return $this->headers->get(PlatformRequest::HEADER_CONTEXT_TOKEN) ?? '';
        }

        return $this->object->get('contextToken');
    }

    public function getRedirectUrl(): ?string
    {
        return $this->object->get('redirectUrl');
    }
}
