<?php declare(strict_types=1);

namespace Laser\Core\Framework\App\Manifest;

use Laser\Core\Framework\App\AppCollection;
use Laser\Core\Framework\App\AppEntity;
use Laser\Core\Framework\App\Exception\AppUrlChangeDetectedException;
use Laser\Core\Framework\App\Hmac\QuerySigner;
use Laser\Core\Framework\App\ShopId\ShopIdProvider;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Laser\Core\Framework\Log\Package;

/**
 * @internal only for use by the app-system, will be considered internal from v6.4.0 onward
 */
#[Package('core')]
class ModuleLoader
{
    public function __construct(
        private readonly EntityRepository $appRepository,
        private readonly ShopIdProvider $shopIdProvider,
        private readonly QuerySigner $querySigner
    ) {
    }

    public function loadModules(Context $context): array
    {
        $criteria = new Criteria();
        $containsModulesFilter = new NotFilter(
            MultiFilter::CONNECTION_AND,
            [
                new EqualsFilter('modules', '[]'),
                new EqualsFilter('mainModule', null),
            ]
        );
        $appActiveFilter = new EqualsFilter('active', true);
        $criteria->addFilter($containsModulesFilter, $appActiveFilter)
            ->addAssociation('translations.language.locale');

        /** @var AppCollection $apps */
        $apps = $this->appRepository->search($criteria, $context)->getEntities();

        return $this->formatPayload($apps, $context);
    }

    private function formatPayload(AppCollection $apps, Context $context): array
    {
        try {
            $this->shopIdProvider->getShopId();
        } catch (AppUrlChangeDetectedException) {
            return [];
        }

        $appModules = [];

        foreach ($apps as $app) {
            $modules = $this->formatModules($app, $context);
            $mainModule = $this->formatMainModule($app, $context);

            if (empty($modules) && !$mainModule) {
                continue;
            }

            $appModules[] = [
                'name' => $app->getName(),
                'label' => $this->mapTranslatedLabels($app),
                'modules' => $modules,
                'mainModule' => $mainModule,
            ];
        }

        return $appModules;
    }

    private function formatModules(AppEntity $app, Context $context): array
    {
        $modules = [];

        foreach ($app->getModules() as $module) {
            $module['source'] = $this->getModuleUrlWithQuery($app, $module, $context);
            $modules[] = $module;
        }

        return $modules;
    }

    private function formatMainModule(AppEntity $app, Context $context): ?array
    {
        if ($app->getMainModule() === null) {
            return null;
        }

        /** @var string $source */
        $source = $app->getMainModule()['source'];
        /** @var string $secret */
        $secret = $app->getAppSecret();

        return [
            'source' => $this->sign($source, $secret, $context),
        ];
    }

    private function mapTranslatedLabels(AppEntity $app): array
    {
        $labels = [];

        foreach ($app->getTranslations() as $translation) {
            $labels[$translation->getLanguage()->getLocale()->getCode()] = $translation->getLabel();
        }

        return $labels;
    }

    private function getModuleUrlWithQuery(AppEntity $app, array $module, Context $context): ?string
    {
        /** @var string|null $registeredSource */
        $registeredSource = $module['source'] ?? null;
        /** @var string $secret */
        $secret = $app->getAppSecret();

        if ($registeredSource === null) {
            return null;
        }

        return $this->sign($registeredSource, $secret, $context);
    }

    private function sign(string $source, string $secret, Context $context): string
    {
        return (string) $this->querySigner->signUri($source, $secret, $context);
    }
}
