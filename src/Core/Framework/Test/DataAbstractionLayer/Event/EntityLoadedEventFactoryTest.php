<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\DataAbstractionLayer\Event;

use PHPUnit\Framework\TestCase;
use Laser\Core\Content\Product\ProductCollection;
use Laser\Core\Content\Product\ProductEntity;
use Laser\Core\Content\Test\Product\ProductBuilder;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent;
use Laser\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEventFactory;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\Test\IdsCollection;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Laser\Core\System\Language\LanguageCollection;
use Laser\Core\System\Language\LanguageEntity;
use Laser\Core\System\Tax\TaxEntity;

/**
 * @internal
 */
class EntityLoadedEventFactoryTest extends TestCase
{
    use IntegrationTestBehaviour;

    private EntityRepository $productRepository;

    private IdsCollection $ids;

    private EntityLoadedEventFactory $entityLoadedEventFactory;

    public function setUp(): void
    {
        $this->productRepository = $this->getContainer()->get('product.repository');
        $this->entityLoadedEventFactory = $this->getContainer()->get(EntityLoadedEventFactory::class);
        $this->ids = new IdsCollection();
    }

    public function testCreate(): void
    {
        $builder = (new ProductBuilder($this->ids, 'p1'))
            ->price(10)
            ->category('c1')
            ->manufacturer('m1')
            ->prices('r1', 5);

        $this->productRepository->create([$builder->build()], Context::createDefaultContext());

        $criteria = new Criteria();
        $criteria->addAssociations([
            'manufacturer',
            'prices',
            'categories',
        ]);

        /** @var ProductEntity $product */
        $product = $this->productRepository->search($criteria, Context::createDefaultContext())->first();
        $product->addExtension('test', new LanguageCollection([
            (new LanguageEntity())->assign(['id' => $this->ids->create('l1'), '_entityName' => 'language']),
        ]));
        $events = $this->entityLoadedEventFactory->create([$product], Context::createDefaultContext());

        $createdEvents = $events->getEvents()->map(fn (EntityLoadedEvent $event): string => $event->getName());
        sort($createdEvents);

        static::assertEquals([
            'category.loaded',
            'language.loaded',
            'product.loaded',
            'product_manufacturer.loaded',
            'product_price.loaded',
            'tax.loaded',
        ], $createdEvents);
    }

    public function testCollectionWithEntitiesMixed(): void
    {
        $tax = (new TaxEntity())->assign(['_entityName' => 'tax']);

        $events = $this->entityLoadedEventFactory->create([new ProductCollection(), $tax], Context::createDefaultContext());

        $createdEvents = $events->getEvents()->map(fn (EntityLoadedEvent $event): string => $event->getName());
        sort($createdEvents);

        static::assertEquals([
            'tax.loaded',
        ], $createdEvents);
    }
}
