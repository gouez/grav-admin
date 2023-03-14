<?php declare(strict_types=1);

namespace Laser\Core\Content\ProductStream\Service;

use Laser\Core\Content\ProductStream\Exception\NoFilterException;
use Laser\Core\Content\ProductStream\ProductStreamEntity;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Exception\EntityNotFoundException;
use Laser\Core\Framework\DataAbstractionLayer\Exception\SearchRequestException;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\Parser\QueryStringParser;
use Laser\Core\Framework\Log\Package;

#[Package('business-ops')]
class ProductStreamBuilder implements ProductStreamBuilderInterface
{
    /**
     * @internal
     */
    public function __construct(
        private readonly EntityRepository $repository,
        private readonly EntityDefinition $productDefinition
    ) {
    }

    public function buildFilters(string $id, Context $context): array
    {
        $criteria = new Criteria([$id]);

        /** @var ProductStreamEntity|null $stream */
        $stream = $this->repository
            ->search($criteria, $context)
            ->get($id);

        if (!$stream) {
            throw new EntityNotFoundException('product_stream', $id);
        }

        $data = $stream->getApiFilter();
        if (!$data) {
            throw new NoFilterException($id);
        }

        $filters = [];
        $exception = new SearchRequestException();

        foreach ($data as $filter) {
            $filters[] = QueryStringParser::fromArray($this->productDefinition, $filter, $exception, '');
        }

        return $filters;
    }
}
