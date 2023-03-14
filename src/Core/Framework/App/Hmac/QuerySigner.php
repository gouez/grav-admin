<?php declare(strict_types=1);

namespace Laser\Core\Framework\App\Hmac;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;
use Laser\Core\Framework\App\Hmac\Guzzle\AuthMiddleware;
use Laser\Core\Framework\App\ShopId\ShopIdProvider;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Store\Authentication\LocaleProvider;

/**
 * @internal only for use by the app-system
 */
#[Package('core')]
class QuerySigner
{
    public function __construct(
        private readonly string $shopUrl,
        private readonly string $laserVersion,
        private readonly LocaleProvider $localeProvider,
        private readonly ShopIdProvider $shopIdProvider
    ) {
    }

    public function signUri(string $uri, string $secret, Context $context): UriInterface
    {
        $uri = Uri::withQueryValues(new Uri($uri), [
            'shop-id' => $this->shopIdProvider->getShopId(),
            'shop-url' => $this->shopUrl,
            'timestamp' => (string) (new \DateTime())->getTimestamp(),
            'sw-version' => $this->laserVersion,
            AuthMiddleware::SHOPWARE_CONTEXT_LANGUAGE => $context->getLanguageId(),
            AuthMiddleware::SHOPWARE_USER_LANGUAGE => $this->localeProvider->getLocaleFromContext($context),
        ]);

        return Uri::withQueryValue(
            $uri,
            'laser-shop-signature',
            (new RequestSigner())->signPayload($uri->getQuery(), $secret)
        );
    }
}
