<?php declare(strict_types=1);

namespace Laser\Core\Framework\Store\Services;

use Doctrine\DBAL\Connection;
use Psr\Http\Message\ResponseInterface;
use Laser\Core\Framework\Api\Context\AdminApiSource;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Store\Exception\StoreSessionExpiredException;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\PlatformRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @internal
 */
#[Package('merchant-services')]
class StoreSessionExpiredMiddleware implements MiddlewareInterface
{
    private const STORE_TOKEN_EXPIRED = 'LaserPlatformException-1';

    /**
     * @internal
     */
    public function __construct(
        private readonly Connection $connection,
        private readonly RequestStack $requestStack
    ) {
    }

    public function __invoke(ResponseInterface $response): ResponseInterface
    {
        if ($response->getStatusCode() !== 401) {
            return $response;
        }

        $body = json_decode($response->getBody()->getContents(), true, 512, \JSON_THROW_ON_ERROR);
        $code = $body['code'] ?? null;

        if ($code !== self::STORE_TOKEN_EXPIRED) {
            $response->getBody()->rewind();

            return $response;
        }

        $this->logoutUser();

        throw new StoreSessionExpiredException();
    }

    private function logoutUser(): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request instanceof Request) {
            return;
        }

        $context = $request->attributes->get(PlatformRequest::ATTRIBUTE_CONTEXT_OBJECT);
        if (!$context instanceof Context) {
            return;
        }

        $source = $context->getSource();
        if (!$source instanceof AdminApiSource) {
            return;
        }

        $userId = $source->getUserId();
        if (!$userId) {
            return;
        }

        $this->connection->executeStatement(
            'UPDATE user SET store_token = NULL WHERE id = :userId',
            ['userId' => Uuid::fromHexToBytes($userId)]
        );
    }
}
