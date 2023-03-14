<?php declare(strict_types=1);

namespace Laser\Core\Framework\App\ShopId;

use Laser\Core\DevOps\Environment\EnvironmentHelper;
use Laser\Core\Framework\App\Exception\AppUrlChangeDetectedException;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Util\Random;
use Laser\Core\System\SystemConfig\SystemConfigService;

/**
 * @internal only for use by the app-system, will be considered internal from v6.4.0 onward
 */
#[Package('core')]
class ShopIdProvider
{
    final public const SHOP_ID_SYSTEM_CONFIG_KEY = 'core.app.shopId';

    public function __construct(
        private readonly SystemConfigService $systemConfigService,
        private readonly EntityRepository $appRepository
    ) {
    }

    /**
     * @throws AppUrlChangeDetectedException
     */
    public function getShopId(): string
    {
        $shopId = $this->systemConfigService->get(self::SHOP_ID_SYSTEM_CONFIG_KEY);

        if (!\is_array($shopId)) {
            $newShopId = $this->generateShopId();
            $this->systemConfigService->set(self::SHOP_ID_SYSTEM_CONFIG_KEY, [
                'app_url' => EnvironmentHelper::getVariable('APP_URL'),
                'value' => $newShopId,
            ]);

            return $newShopId;
        }

        if (EnvironmentHelper::getVariable('APP_URL') !== ($shopId['app_url'] ?? '')) {
            if ($this->hasApps()) {
                /** @var string $appUrl */
                $appUrl = EnvironmentHelper::getVariable('APP_URL');

                throw new AppUrlChangeDetectedException($shopId['app_url'], $appUrl);
            }

            // if the shop does not have any apps we can update the existing shop id value
            // with the new APP_URL as no app knows the shop id
            $this->systemConfigService->set(ShopIdProvider::SHOP_ID_SYSTEM_CONFIG_KEY, [
                'app_url' => EnvironmentHelper::getVariable('APP_URL'),
                'value' => $shopId['value'],
            ]);
        }

        return $shopId['value'];
    }

    private function generateShopId(): string
    {
        return Random::getAlphanumericString(16);
    }

    private function hasApps(): bool
    {
        $criteria = new Criteria();
        $criteria->setLimit(1);

        $result = $this->appRepository->searchIds($criteria, Context::createDefaultContext());

        return $result->firstId() !== null;
    }
}
