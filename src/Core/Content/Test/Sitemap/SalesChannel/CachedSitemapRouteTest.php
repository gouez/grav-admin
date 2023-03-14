<?php declare(strict_types=1);

namespace Laser\Core\Content\Test\Sitemap\SalesChannel;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Laser\Core\Content\Sitemap\SalesChannel\AbstractSitemapRoute;
use Laser\Core\Content\Sitemap\SalesChannel\CachedSitemapRoute;
use Laser\Core\Content\Sitemap\SalesChannel\SitemapRouteResponse;
use Laser\Core\Content\Sitemap\Service\SitemapExporter;
use Laser\Core\Content\Sitemap\Service\SitemapExporterInterface;
use Laser\Core\Content\Test\Product\ProductBuilder;
use Laser\Core\Defaults;
use Laser\Core\Framework\Adapter\Cache\CacheTracer;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\Cache\EntityCacheKeyGenerator;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Test\IdsCollection;
use Laser\Core\Framework\Test\TestCaseBase\DatabaseTransactionBehaviour;
use Laser\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextFactory;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Laser\Core\System\SystemConfig\SystemConfigService;
use Laser\Core\Test\TestDefaults;
use Laser\Storefront\Framework\Seo\SeoUrlRoute\ProductPageSeoUrlRoute;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 *
 * @group cache
 * @group store-api
 */
#[Package('sales-channel')]
class CachedSitemapRouteTest extends TestCase
{
    use KernelTestBehaviour;

    use DatabaseTransactionBehaviour;

    private SalesChannelContext $context;

    protected function setUp(): void
    {
        if (!$this->getContainer()->has(ProductPageSeoUrlRoute::class)) {
            static::markTestSkipped('NEXT-16799: Sitemap module has a dependency on storefront routes');
        }
        parent::setUp();
    }

    /**
     * @afterClass
     */
    public function cleanup(): void
    {
        $this->getContainer()->get('cache.object')
            ->invalidateTags([CachedSitemapRoute::ALL_TAG]);
    }

    /**
     * @dataProvider invalidationProvider
     */
    public function testInvalidation(\Closure $before, \Closure $after, int $calls, int $strategy = SitemapExporterInterface::STRATEGY_SCHEDULED_TASK): void
    {
        $this->getContainer()->get('cache.object')
            ->invalidateTags([CachedSitemapRoute::ALL_TAG]);

        $ids = new IdsCollection();

        $snippetSetId = $this->getContainer()->get(Connection::class)
            ->fetchOne('SELECT LOWER(HEX(id)) FROM snippet_set LIMIT 1');

        $domain = [
            'url' => 'http://laser.test',
            'salesChannelId' => TestDefaults::SALES_CHANNEL,
            'languageId' => Defaults::LANGUAGE_SYSTEM,
            'currencyId' => Defaults::CURRENCY,
            'snippetSetId' => $snippetSetId,
        ];

        $this->getContainer()->get('sales_channel_domain.repository')
            ->create([$domain], Context::createDefaultContext());

        $this->context = $this->getContainer()->get(SalesChannelContextFactory::class)
            ->create(Uuid::randomHex(), TestDefaults::SALES_CHANNEL);

        $products = [
            (new ProductBuilder($ids, 'first'))
                ->price(100)
                ->visibility()
                ->build(),
            (new ProductBuilder($ids, 'second'))
                ->price(100)
                ->visibility()
                ->build(),
        ];

        $this->getContainer()->get('product.repository')
            ->create($products, Context::createDefaultContext());

        $counter = new SitemapRouteCounter(
            $this->getContainer()->get('Laser\Core\Content\Sitemap\SalesChannel\CachedSitemapRoute.inner')
        );

        $config = $this->createMock(SystemConfigService::class);
        $config->expects(static::any())
            ->method('getInt')
            ->with('core.sitemap.sitemapRefreshStrategy')
            ->willReturn($strategy);

        $route = new CachedSitemapRoute(
            $counter,
            $this->getContainer()->get('cache.object'),
            $this->getContainer()->get(EntityCacheKeyGenerator::class),
            $this->getContainer()->get(CacheTracer::class),
            $this->getContainer()->get('event_dispatcher'),
            [],
            $config
        );

        $before($this->context, $this->getContainer());

        $route->load(new Request(), $this->context);
        $route->load(new Request(), $this->context);

        $after($this->context, $this->getContainer());

        $route->load(new Request(), $this->context);
        $route->load(new Request(), $this->context);

        static::assertSame($calls, $counter->getCount());
    }

    public static function invalidationProvider(): \Generator
    {
        yield 'Cache invalidated if sitemap generated' => [
            function (): void {
            },
            function (SalesChannelContext $context, ContainerInterface $container): void {
                $container->get(SitemapExporter::class)->generate($context, true);
            },
            2,
        ];

        yield 'Sitemap not cached for live strategy' => [
            function (): void {
            },
            function (): void {
            },
            4,
            SitemapExporterInterface::STRATEGY_LIVE,
        ];
    }
}

/**
 * @internal
 */
class SitemapRouteCounter extends AbstractSitemapRoute
{
    public int $count = 0;

    public function __construct(private readonly AbstractSitemapRoute $decorated)
    {
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function load(Request $request, SalesChannelContext $context): SitemapRouteResponse
    {
        ++$this->count;

        return $this->getDecorated()->load($request, $context);
    }

    public function getDecorated(): AbstractSitemapRoute
    {
        return $this->decorated;
    }
}
