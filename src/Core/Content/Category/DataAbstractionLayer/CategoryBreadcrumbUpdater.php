<?php declare(strict_types=1);

namespace Laser\Core\Content\Category\DataAbstractionLayer;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Laser\Core\Content\Category\CategoryCollection;
use Laser\Core\Content\Category\Exception\CategoryNotFoundException;
use Laser\Core\Defaults;
use Laser\Core\Framework\Api\Context\SystemSource;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\Doctrine\RetryableQuery;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\System\Language\LanguageEntity;

#[Package('content')]
class CategoryBreadcrumbUpdater
{
    /**
     * @internal
     */
    public function __construct(
        private readonly Connection $connection,
        private readonly EntityRepository $categoryRepository,
        private readonly EntityRepository $languageRepository
    ) {
    }

    public function update(array $ids, Context $context): void
    {
        if (empty($ids)) {
            return;
        }

        $versionId = Uuid::fromHexToBytes($context->getVersionId());

        $query = $this->connection->createQueryBuilder();
        $query->select('category.path');
        $query->from('category');
        $query->where('category.id IN (:ids)');
        $query->andWhere('category.version_id = :version');
        $query->setParameter('version', $versionId);
        $query->setParameter('ids', Uuid::fromHexToBytesList($ids), ArrayParameterType::STRING);

        $paths = $query->executeQuery()->fetchFirstColumn();

        $all = $ids;
        foreach ($paths as $path) {
            $path = explode('|', (string) $path);
            foreach ($path as $id) {
                $all[] = $id;
            }
        }

        $all = array_filter(array_values(array_keys(array_flip($all))));

        $languages = $this->languageRepository->search(new Criteria(), $context);

        /** @var LanguageEntity $language */
        foreach ($languages as $language) {
            $context = new Context(
                new SystemSource(),
                [],
                Defaults::CURRENCY,
                array_filter([$language->getId(), $language->getParentId(), Defaults::LANGUAGE_SYSTEM]),
                Defaults::LIVE_VERSION
            );

            $this->updateLanguage($ids, $context, $all);
        }
    }

    private function updateLanguage(array $ids, Context $context, array $all): void
    {
        $versionId = Uuid::fromHexToBytes($context->getVersionId());
        $languageId = Uuid::fromHexToBytes($context->getLanguageId());

        /** @var CategoryCollection $categories */
        $categories = $this->categoryRepository
            ->search(new Criteria($all), $context)
            ->getEntities();

        $update = $this->connection->prepare('
            INSERT INTO `category_translation` (`category_id`, `category_version_id`, `language_id`, `breadcrumb`, `created_at`)
            VALUES (:categoryId, :versionId, :languageId, :breadcrumb, DATE(NOW()))
            ON DUPLICATE KEY UPDATE `breadcrumb` = :breadcrumb
        ');
        $update = new RetryableQuery($this->connection, $update);

        foreach ($ids as $id) {
            try {
                $path = $this->buildBreadcrumb($id, $categories);
            } catch (CategoryNotFoundException) {
                continue;
            }

            $update->execute([
                'categoryId' => Uuid::fromHexToBytes($id),
                'versionId' => $versionId,
                'languageId' => $languageId,
                'breadcrumb' => json_encode($path, \JSON_THROW_ON_ERROR),
            ]);
        }
    }

    private function buildBreadcrumb(string $id, CategoryCollection $categories): array
    {
        $category = $categories->get($id);

        if (!$category) {
            throw new CategoryNotFoundException($id);
        }

        $breadcrumb = [];
        if ($category->getParentId()) {
            $breadcrumb = $this->buildBreadcrumb($category->getParentId(), $categories);
        }

        $breadcrumb[$category->getId()] = $category->getTranslation('name');

        return $breadcrumb;
    }
}
