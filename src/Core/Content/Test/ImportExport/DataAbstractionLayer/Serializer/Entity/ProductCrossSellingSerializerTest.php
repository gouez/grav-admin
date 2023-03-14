<?php declare(strict_types=1);

namespace Laser\Core\Content\Test\ImportExport\DataAbstractionLayer\Serializer\Entity;

use PHPUnit\Framework\TestCase;
use Laser\Core\Content\ImportExport\DataAbstractionLayer\Serializer\Entity\ProductCrossSellingSerializer;
use Laser\Core\Content\ImportExport\DataAbstractionLayer\Serializer\SerializerRegistry;
use Laser\Core\Content\ImportExport\Struct\Config;
use Laser\Core\Content\Product\Aggregate\ProductCrossSelling\ProductCrossSellingDefinition;
use Laser\Core\Content\Product\Aggregate\ProductCrossSelling\ProductCrossSellingEntity;
use Laser\Core\Content\Product\Aggregate\ProductCrossSellingAssignedProducts\ProductCrossSellingAssignedProductsEntity;
use Laser\Core\Content\Test\Product\ProductBuilder;
use Laser\Core\Defaults;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Laser\Core\Framework\Test\TestDataCollection;
use Laser\Core\Framework\Uuid\Uuid;

/**
 * @internal
 */
#[Package('system-settings')]
class ProductCrossSellingSerializerTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function testOnlySupportsProductCrossSelling(): void
    {
        /** @var EntityRepository $assignedProductsRepository */
        $assignedProductsRepository = $this->getContainer()->get('product_cross_selling_assigned_products.repository');

        $serializer = new ProductCrossSellingSerializer($assignedProductsRepository);

        static::assertTrue($serializer->supports(ProductCrossSellingDefinition::ENTITY_NAME), 'should support product cross selling');

        $definitionRegistry = $this->getContainer()->get(DefinitionInstanceRegistry::class);
        foreach ($definitionRegistry->getDefinitions() as $definition) {
            $entity = $definition->getEntityName();
            if ($entity !== ProductCrossSellingDefinition::ENTITY_NAME) {
                static::assertFalse(
                    $serializer->supports($definition->getEntityName()),
                    ProductCrossSellingSerializer::class . ' should not support ' . $entity
                );
            }
        }
    }

    public function testCrossSellingSerialize(): void
    {
        $crossSelling = $this->getProductCrossSelling();

        /** @var EntityRepository $assignedProductsRepository */
        $assignedProductsRepository = $this->getContainer()->get('product_cross_selling_assigned_products.repository');
        $productCrossSellingDefinition = $this->getContainer()->get(ProductCrossSellingDefinition::class);

        $serializer = new ProductCrossSellingSerializer($assignedProductsRepository);
        $serializer->setRegistry($this->getContainer()->get(SerializerRegistry::class));

        $serialized = iterator_to_array($serializer->serialize(new Config([], [], []), $productCrossSellingDefinition, $crossSelling));

        static::assertNotEmpty($serialized);

        $assignedProducts = $crossSelling->getAssignedProducts();
        $assignedProducts->sort(fn (ProductCrossSellingAssignedProductsEntity $a, ProductCrossSellingAssignedProductsEntity $b) => $a->getPosition() <=> $b->getPosition());
        $productsIds = $assignedProducts->map(fn (ProductCrossSellingAssignedProductsEntity $assignedProductsEntity) => $assignedProductsEntity->getProductId());

        static::assertSame($crossSelling->getId(), $serialized['id']);
        static::assertSame($crossSelling->getProductId(), $serialized['productId']);
        static::assertSame(implode('|', $productsIds), $serialized['assignedProducts']);

        $deserialized = $serializer->deserialize(new Config([], [], []), $productCrossSellingDefinition, $serialized);

        static::assertSame($crossSelling->getId(), $deserialized['id']);
        static::assertSame($crossSelling->getProductId(), $deserialized['productId']);
        static::assertSame(array_values($productsIds), array_column($deserialized['assignedProducts'], 'productId'));
    }

    private function getProductCrossSelling(): ProductCrossSellingEntity
    {
        $ids = new TestDataCollection();

        $data = [
            (new ProductBuilder($ids, 'a'))->price(15, 10)->visibility()->build(),
            (new ProductBuilder($ids, 'b'))->price(15, 10)->visibility()->build(),
            (new ProductBuilder($ids, 'c'))->price(15, 10)->visibility()->build(),
            (new ProductBuilder($ids, 'd'))->price(15, 10)->visibility()->build(),
            (new ProductBuilder($ids, 'e'))->price(15, 10)->visibility()->build(),
        ];

        /** @var EntityRepository $productRepository */
        $productRepository = $this->getContainer()->get('product.repository');
        $productRepository->create($data, Context::createDefaultContext());

        $crossSellingId = Uuid::randomHex();

        $crossSelling = [
            'id' => $crossSellingId,
            'productId' => $ids->get('a'),
            'active' => true,
            'translations' => [
                Defaults::LANGUAGE_SYSTEM => [
                    'name' => 'test cross selling',
                ],
            ],
            'type' => 'productList',
            'position' => 1,
            'limit' => 500,
            'sortBy' => 'name',
            'sortDirection' => 'ASC',
            'assignedProducts' => [
                ['productId' => $ids->get('b'), 'position' => 0],
                ['productId' => $ids->get('c'), 'position' => 1],
                ['productId' => $ids->get('d'), 'position' => 2],
                ['productId' => $ids->get('e'), 'position' => 3],
            ],
        ];

        /** @var EntityRepository $crossSellingRepository */
        $crossSellingRepository = $this->getContainer()->get('product_cross_selling.repository');
        $crossSellingRepository->create([$crossSelling], Context::createDefaultContext());

        $criteria = new Criteria([$crossSellingId]);
        $criteria->addAssociation('assignedProducts');

        return $crossSellingRepository->search($criteria, Context::createDefaultContext())->first();
    }
}
