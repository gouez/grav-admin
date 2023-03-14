<?php declare(strict_types=1);

namespace Laser\Core\Content\Test;

use Laser\Core\Content\Product\ProductDefinition;
use Laser\Core\Content\Product\ProductEntity;
use Laser\Core\Content\Seo\SeoUrlRoute\SeoUrlMapping;
use Laser\Core\Content\Seo\SeoUrlRoute\SeoUrlRouteConfig;
use Laser\Core\Content\Seo\SeoUrlRoute\SeoUrlRouteInterface;
use Laser\Core\Framework\DataAbstractionLayer\Entity;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\System\SalesChannel\SalesChannelEntity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @internal
 */
#[Route(defaults: ['_routeScope' => ['store-api']])]
class TestProductSeoUrlRoute implements SeoUrlRouteInterface
{
    final public const ROUTE_NAME = 'test.product.page';
    final public const DEFAULT_TEMPLATE = '{{ product.id }}';

    public function __construct(private readonly ProductDefinition $productDefinition)
    {
    }

    #[Route(path: '/test/{productId}', name: 'test.product.page', options: ['seo' => true], methods: ['GET'])]
    public function route(): Response
    {
        return new Response();
    }

    public function getConfig(): SeoUrlRouteConfig
    {
        return new SeoUrlRouteConfig(
            $this->productDefinition,
            self::ROUTE_NAME,
            self::DEFAULT_TEMPLATE,
            true
        );
    }

    public function prepareCriteria(Criteria $criteria, SalesChannelEntity $salesChannel): void
    {
        // no-op, dummy implementation
    }

    /**
     * @param ProductEntity $entity
     */
    public function getMapping(Entity $entity, ?SalesChannelEntity $salesChannel): SeoUrlMapping
    {
        return new SeoUrlMapping(
            $entity,
            ['productId' => $entity->getId()],
            ['product' => $entity->jsonSerialize()]
        );
    }
}
