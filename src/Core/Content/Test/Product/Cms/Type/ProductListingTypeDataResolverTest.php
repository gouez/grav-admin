<?php declare(strict_types=1);

namespace Laser\Core\Content\Test\Product\Cms\Type;

use PHPUnit\Framework\TestCase;
use Laser\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Laser\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Laser\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Laser\Core\Content\Cms\SalesChannel\Struct\ProductListingStruct;
use Laser\Core\Content\Product\Cms\ProductListingCmsElementResolver;
use Laser\Core\Content\Product\SalesChannel\Listing\ProductListingResult;
use Laser\Core\Content\Product\SalesChannel\Listing\ProductListingRoute;
use Laser\Core\Content\Product\SalesChannel\Listing\ProductListingRouteResponse;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityCollection;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
class ProductListingTypeDataResolverTest extends TestCase
{
    private ProductListingCmsElementResolver $listingResolver;

    protected function setUp(): void
    {
        $mock = $this->createMock(ProductListingRoute::class);
        $mock->method('load')->willReturn(
            new ProductListingRouteResponse(
                new ProductListingResult('product', 0, new EntityCollection(), null, new Criteria(), Context::createDefaultContext())
            )
        );

        $this->listingResolver = new ProductListingCmsElementResolver($mock);
    }

    public function testGetType(): void
    {
        static::assertEquals('product-listing', $this->listingResolver->getType());
    }

    public function testCollect(): void
    {
        $resolverContext = new ResolverContext($this->createMock(SalesChannelContext::class), new Request());

        $slot = new CmsSlotEntity();
        $slot->setUniqueIdentifier('id');
        $slot->setType('product-listing');

        $collection = $this->listingResolver->collect($slot, $resolverContext);

        static::assertNull($collection);
    }

    public function testEnrichWithoutListingContext(): void
    {
        $resolverContext = new ResolverContext($this->createMock(SalesChannelContext::class), new Request());
        $result = new ElementDataCollection();

        $slot = new CmsSlotEntity();
        $slot->setUniqueIdentifier('id');
        $slot->setType('product-listing');

        $this->listingResolver->enrich($slot, $resolverContext, $result);

        /** @var ProductListingStruct|null $productListingStruct */
        $productListingStruct = $slot->getData();
        static::assertInstanceOf(ProductListingStruct::class, $productListingStruct);
        static::assertInstanceOf(EntitySearchResult::class, $productListingStruct->getListing());
    }
}
