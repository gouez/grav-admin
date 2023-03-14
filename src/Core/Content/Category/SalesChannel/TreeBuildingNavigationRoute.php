<?php declare(strict_types=1);

namespace Laser\Core\Content\Category\SalesChannel;

use Laser\Core\Content\Category\CategoryCollection;
use Laser\Core\Content\Category\CategoryEntity;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Laser\Core\System\SalesChannel\SalesChannelEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(defaults: ['_routeScope' => ['store-api']])]
#[Package('content')]
class TreeBuildingNavigationRoute extends AbstractNavigationRoute
{
    /**
     * @internal
     */
    public function __construct(private readonly AbstractNavigationRoute $decorated)
    {
    }

    public function getDecorated(): AbstractNavigationRoute
    {
        return $this->decorated;
    }

    #[Route(path: '/store-api/navigation/{activeId}/{rootId}', name: 'store-api.navigation', methods: ['GET', 'POST'], defaults: ['_entity' => 'payment_method'])]
    public function load(string $activeId, string $rootId, Request $request, SalesChannelContext $context, Criteria $criteria): NavigationRouteResponse
    {
        $activeId = $this->resolveAliasId($activeId, $context->getSalesChannel());

        $rootId = $this->resolveAliasId($rootId, $context->getSalesChannel());

        $response = $this->getDecorated()->load($activeId, $rootId, $request, $context, $criteria);

        $buildTree = $request->query->getBoolean('buildTree', $request->request->getBoolean('buildTree', true));

        if (!$buildTree) {
            return $response;
        }

        $categories = $this->buildTree($rootId, $response->getCategories()->getElements());

        return new NavigationRouteResponse($categories);
    }

    /**
     * @param CategoryEntity[] $categories
     */
    private function buildTree(?string $parentId, array $categories): CategoryCollection
    {
        $children = new CategoryCollection();
        foreach ($categories as $key => $category) {
            if ($category->getParentId() !== $parentId) {
                continue;
            }

            unset($categories[$key]);

            $children->add($category);
        }

        $children->sortByPosition();

        $items = new CategoryCollection();
        foreach ($children as $child) {
            if (!$child->getActive() || !$child->getVisible()) {
                continue;
            }

            $child->setChildren($this->buildTree($child->getId(), $categories));

            $items->add($child);
        }

        return $items;
    }

    private function resolveAliasId(string $id, SalesChannelEntity $salesChannelEntity): string
    {
        switch ($id) {
            case 'main-navigation':
                return $salesChannelEntity->getNavigationCategoryId();
            case 'service-navigation':
                if ($salesChannelEntity->getServiceCategoryId() === null) {
                    throw new \RuntimeException(\sprintf('Service category, for sales channel %s, is not set', $salesChannelEntity->getTranslation('name')));
                }

                return $salesChannelEntity->getServiceCategoryId();
            case 'footer-navigation':
                if ($salesChannelEntity->getFooterCategoryId() === null) {
                    throw new \RuntimeException(\sprintf('Footer category, for sales channel %s, is not set', $salesChannelEntity->getTranslation('name')));
                }

                return $salesChannelEntity->getFooterCategoryId();
            default:
                return $id;
        }
    }
}
