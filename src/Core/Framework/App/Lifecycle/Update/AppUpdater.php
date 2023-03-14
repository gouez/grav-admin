<?php declare(strict_types=1);

namespace Laser\Core\Framework\App\Lifecycle\Update;

use Laser\Core\Framework\App\AppEntity;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Plugin\Exception\DecorationPatternException;
use Laser\Core\Framework\Store\Exception\ExtensionUpdateRequiresConsentAffirmationException;
use Laser\Core\Framework\Store\Services\AbstractExtensionDataProvider;
use Laser\Core\Framework\Store\Services\AbstractStoreAppLifecycleService;
use Laser\Core\Framework\Store\Services\ExtensionDownloader;
use Laser\Core\Framework\Store\Struct\ExtensionStruct;

/**
 * @internal
 */
#[Package('core')]
class AppUpdater extends AbstractAppUpdater
{
    public function __construct(
        private readonly AbstractExtensionDataProvider $extensionDataProvider,
        private readonly EntityRepository $appRepo,
        private readonly ExtensionDownloader $downloader,
        private readonly AbstractStoreAppLifecycleService $appLifecycle
    ) {
    }

    public function updateApps(Context $context): void
    {
        $extensions = $this->extensionDataProvider->getInstalledExtensions($context, true);
        $extensions = $extensions->filterByType(ExtensionStruct::EXTENSION_TYPE_APP);

        $outdatedApps = [];

        foreach ($extensions->getIterator() as $extension) {
            $id = $extension->getLocalId();
            if (!$id) {
                continue;
            }
            /** @var AppEntity $localApp */
            $localApp = $this->appRepo->search(new Criteria([$id]), $context)->first();
            $nextVersion = $extension->getLatestVersion();
            if (!$nextVersion) {
                continue;
            }

            if (version_compare($nextVersion, $localApp->getVersion()) > 0) {
                $outdatedApps[] = $extension;
            }
        }
        foreach ($outdatedApps as $app) {
            $this->downloader->download($app->getName(), $context);

            try {
                $this->appLifecycle->updateExtension($app->getName(), false, $context);
            } catch (ExtensionUpdateRequiresConsentAffirmationException) {
                //nth
            }
        }
    }

    protected function getDecorated(): AbstractAppUpdater
    {
        throw new DecorationPatternException(self::class);
    }
}
