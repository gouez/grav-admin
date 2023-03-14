<?php declare(strict_types=1);

namespace Laser\Core\Content\Test\Product\SalesChannel\Sorting;

use PHPUnit\Framework\TestCase;
use Laser\Core\Content\Product\Exception\DuplicateProductSortingKeyException;
use Laser\Core\Content\Product\SalesChannel\Sorting\ProductSortingEntity;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;

/**
 * @internal
 */
class ProductListingSortingTest extends TestCase
{
    use IntegrationTestBehaviour;

    /**
     * @var EntityRepository
     */
    private $productSortingRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->productSortingRepository = $this->getContainer()->get('product_sorting.repository');
    }

    public function testProductSortingFieldPriority(): void
    {
        $productSortingEntity = new ProductSortingEntity();
        $productSortingEntity->setFields(
            [
                ['field' => 'product.name', 'order' => 'asc', 'priority' => 1, 'naturalSorting' => 1],
                ['field' => 'product.cheapestPrice', 'order' => 'asc', 'priority' => 1000, 'naturalSorting' => 1],
            ]
        );

        /** @var FieldSorting[] $sortings */
        $sortings = $productSortingEntity->createDalSorting();

        static::assertCount(2, $sortings);
        static::assertEquals('product.cheapestPrice', $sortings[0]->getField());
        static::assertEquals('product.name', $sortings[1]->getField());
    }

    public function testDuplicateProductSortingKey(): void
    {
        $productSortingKey = Uuid::randomHex();

        $data = [
            'id' => Uuid::randomHex(),
            'key' => $productSortingKey,
            'priority' => 0,
            'active' => true,
            'fields' => [
                ['field' => 'product.name', 'order' => 'asc', 'priority' => 1, 'naturalSorting' => 1],
            ],
            'label' => 'test',
        ];

        $this->productSortingRepository->create([$data], Context::createDefaultContext());

        $data = [
            'id' => Uuid::randomHex(),
            'key' => $productSortingKey,
            'name' => 'product',
            'priority' => 0,
            'active' => true,
            'fields' => [
                ['field' => 'product.name', 'order' => 'asc', 'priority' => 1, 'naturalSorting' => 1],
            ],
            'label' => 'test',
        ];

        $this->expectException(DuplicateProductSortingKeyException::class);
        $this->expectExceptionMessage('Sorting with key "' . $productSortingKey . '" already exists.');

        $this->productSortingRepository->create([$data], Context::createDefaultContext());
    }
}
