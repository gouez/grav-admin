<?php declare(strict_types=1);

namespace Laser\Core\Framework\App\Lifecycle\Persister;

use Laser\Core\Framework\App\AppEntity;
use Laser\Core\Framework\App\Manifest\Manifest;
use Laser\Core\Framework\App\Template\AbstractTemplateLoader;
use Laser\Core\Framework\App\Template\TemplateCollection;
use Laser\Core\Framework\App\Template\TemplateEntity;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\Log\Package;

/**
 * @internal only for use by the app-system, will be considered internal from v6.4.0 onward
 */
#[Package('core')]
class TemplatePersister
{
    public function __construct(
        private readonly AbstractTemplateLoader $templateLoader,
        private readonly EntityRepository $templateRepository,
        private readonly EntityRepository $appRepository
    ) {
    }

    public function updateTemplates(Manifest $manifest, string $appId, Context $context): void
    {
        $app = $this->getAppWithExistingTemplates($appId, $context);
        /** @var TemplateCollection $existingTemplates */
        $existingTemplates = $app->getTemplates();
        $templatePaths = $this->templateLoader->getTemplatePathsForApp($manifest);

        $upserts = [];
        foreach ($templatePaths as $templatePath) {
            $payload = [
                'template' => $this->templateLoader->getTemplateContent($templatePath, $manifest),
            ];

            /** @var TemplateEntity|null $existing */
            $existing = $existingTemplates->filterByProperty('path', $templatePath)->first();
            if ($existing) {
                $payload['id'] = $existing->getId();
                $existingTemplates->remove($existing->getId());
            } else {
                $payload['appId'] = $appId;
                $payload['active'] = $app->isActive();
                $payload['path'] = $templatePath;
            }

            $upserts[] = $payload;
        }

        if (!empty($upserts)) {
            $this->templateRepository->upsert($upserts, $context);
        }

        $this->deleteOldTemplates($existingTemplates, $context);
    }

    private function deleteOldTemplates(TemplateCollection $toBeRemoved, Context $context): void
    {
        /** @var array<string> $ids */
        $ids = $toBeRemoved->getIds();

        if (!empty($ids)) {
            $ids = array_map(static fn (string $id): array => ['id' => $id], array_values($ids));

            $this->templateRepository->delete($ids, $context);
        }
    }

    private function getAppWithExistingTemplates(string $appId, Context $context): AppEntity
    {
        $criteria = new Criteria([$appId]);
        $criteria->addAssociation('templates');

        /** @var AppEntity $app */
        $app = $this->appRepository->search($criteria, $context)->first();

        return $app;
    }
}
