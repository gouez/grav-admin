<?php declare(strict_types=1);

namespace Laser\Core\Content\Test\Product\Cms\Type;

use PHPUnit\Framework\TestCase;
use Laser\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Laser\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Laser\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Laser\Core\Content\Cms\SalesChannel\Struct\ProductDescriptionReviewsStruct;
use Laser\Core\Content\Product\Cms\ProductDescriptionReviewsCmsElementResolver;
use Laser\Core\Content\Product\SalesChannel\Review\AbstractProductReviewRoute;
use Laser\Core\Content\Product\SalesChannel\Review\ProductReviewRouteResponse;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityCollection;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
class ProductDescriptionReviewsTypeDataResolverTest extends TestCase
{
    use IntegrationTestBehaviour;

    private ProductDescriptionReviewsCmsElementResolver $productDescriptionReviewResolver;

    protected function setUp(): void
    {
        $productReviewRouteMock = $this->createMock(AbstractProductReviewRoute::class);
        $productReviewRouteMock->method('load')->willReturn(
            new ProductReviewRouteResponse(
                new EntitySearchResult('product', 0, new EntityCollection(), null, new Criteria(), Context::createDefaultContext())
            )
        );

        $this->productDescriptionReviewResolver = new ProductDescriptionReviewsCmsElementResolver(
            $productReviewRouteMock
        );
    }

    public function testType(): void
    {
        static::assertSame('product-description-reviews', $this->productDescriptionReviewResolver->getType());
    }

    public function testCollect(): void
    {
        $resolverContext = new ResolverContext($this->createMock(SalesChannelContext::class), new Request());

        $slot = new CmsSlotEntity();
        $slot->setUniqueIdentifier('id');
        $slot->setType('product-description-reviews');

        $collection = $this->productDescriptionReviewResolver->collect($slot, $resolverContext);

        static::assertNull($collection);
    }

    public function testEnrichWithoutContext(): void
    {
        $resolverContext = new ResolverContext($this->createMock(SalesChannelContext::class), new Request());
        $result = new ElementDataCollection();

        $slot = new CmsSlotEntity();
        $slot->setUniqueIdentifier('id');
        $slot->setType('product-description-reviews');

        $this->productDescriptionReviewResolver->enrich($slot, $resolverContext, $result);

        /** @var ProductDescriptionReviewsStruct|null $productDescriptionReviewStruct */
        $productDescriptionReviewStruct = $slot->getData();
        static::assertInstanceOf(ProductDescriptionReviewsStruct::class, $productDescriptionReviewStruct);
        static::assertNull($productDescriptionReviewStruct->getProduct());
    }
}
