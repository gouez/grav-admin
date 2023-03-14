<?php declare(strict_types=1);

namespace Laser\Core\Framework\App\Lifecycle\Registration;

use Laser\Core\Framework\App\Exception\AppRegistrationException;
use Laser\Core\Framework\App\Exception\AppUrlChangeDetectedException;
use Laser\Core\Framework\App\Manifest\Manifest;
use Laser\Core\Framework\App\ShopId\ShopIdProvider;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Store\Services\StoreClient;

/**
 * @internal only for use by the app-system, will be considered internal from v6.4.0 onward
 */
#[Package('core')]
final class HandshakeFactory
{
    public function __construct(
        private readonly string $shopUrl,
        private readonly ShopIdProvider $shopIdProvider,
        private readonly StoreClient $storeClient,
        private readonly string $laserVersion
    ) {
    }

    public function create(Manifest $manifest): AppHandshakeInterface
    {
        $setup = $manifest->getSetup();
        $metadata = $manifest->getMetadata();

        if (!$setup) {
            throw new AppRegistrationException(
                sprintf('No setup for registration provided in manifest for app "%s".', $metadata->getName())
            );
        }

        $privateSecret = $setup->getSecret();

        try {
            $shopId = $this->shopIdProvider->getShopId();
        } catch (AppUrlChangeDetectedException) {
            throw new AppRegistrationException(
                'The app url changed. Please resolve how the apps should handle this change.'
            );
        }

        if ($privateSecret) {
            return new PrivateHandshake(
                $this->shopUrl,
                $privateSecret,
                $setup->getRegistrationUrl(),
                $metadata->getName(),
                $shopId,
                $this->laserVersion
            );
        }

        return new StoreHandshake(
            $this->shopUrl,
            $setup->getRegistrationUrl(),
            $metadata->getName(),
            $shopId,
            $this->storeClient,
            $this->laserVersion
        );
    }
}
