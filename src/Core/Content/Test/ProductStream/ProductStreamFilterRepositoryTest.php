<?php declare(strict_types=1);

namespace Laser\Core\Content\Test\ProductStream;

use PHPUnit\Framework\TestCase;
use Laser\Core\Content\ProductStream\ProductStreamEntity;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;

/**
 * @internal
 */
#[Package('business-ops')]
class ProductStreamFilterRepositoryTest extends TestCase
{
    use IntegrationTestBehaviour;

    /**
     * @var EntityRepository
     */
    private $repository;

    private string $streamId;

    private Context $context;

    /**
     * @var EntityRepository
     */
    private $productStreamRepository;

    protected function setUp(): void
    {
        $this->repository = $this->getContainer()->get('product_stream_filter.repository');
        $this->productStreamRepository = $this->getContainer()->get('product_stream.repository');
        $this->streamId = Uuid::randomHex();
        $this->context = Context::createDefaultContext();
        $this->productStreamRepository->upsert([['id' => $this->streamId, 'name' => 'Test stream']], $this->context);
    }

    public function testCreateEntity(): void
    {
        $id = Uuid::randomHex();
        $this->repository->create([
            ['id' => $id, 'type' => 'equals', 'value' => 'awesome', 'field' => 'product.name', 'productStreamId' => $this->streamId],
        ], $this->context);

        /** @var ProductStreamEntity $entity */
        $entity = $this->productStreamRepository->search(new Criteria([$this->streamId]), $this->context)->get($this->streamId);
        static::assertSame([['type' => 'equals', 'field' => 'product.name', 'value' => 'awesome']], $entity->getApiFilter());
    }

    public function testUpdateEntity(): void
    {
        $id = Uuid::randomHex();
        $this->repository->create([
            ['id' => $id, 'type' => 'equals', 'value' => 'new awesome', 'field' => 'product.name', 'productStreamId' => $this->streamId],
        ], $this->context);
        $this->repository->upsert([
            ['id' => $id, 'type' => 'range', 'field' => 'product.weight', 'parameters' => [RangeFilter::GT => 0.5, RangeFilter::LT => 100], 'productStreamId' => $this->streamId],
        ], $this->context);

        /** @var ProductStreamEntity $entity */
        $entity = $this->productStreamRepository->search(new Criteria([$this->streamId]), $this->context)->get($this->streamId);
        static::assertSame([['type' => 'range', 'field' => 'product.weight', 'parameters' => [RangeFilter::GT => 0.5, RangeFilter::LT => 100]]], $entity->getApiFilter());
    }

    public function testRangeEntity(): void
    {
        $id = Uuid::randomHex();
        $this->repository->create([
            ['id' => $id, 'type' => 'range', 'parameters' => [RangeFilter::GT => 0.5, RangeFilter::LT => 100], 'field' => 'product.weight', 'productStreamId' => $this->streamId],
        ], $this->context);

        /** @var ProductStreamEntity $entity */
        $entity = $this->productStreamRepository->search(new Criteria([$this->streamId]), $this->context)->get($this->streamId);
        static::assertSame([['type' => 'range', 'field' => 'product.weight', 'parameters' => [RangeFilter::GT => 0.5, RangeFilter::LT => 100]]], $entity->getApiFilter());
    }
}
