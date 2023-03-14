<?php declare(strict_types=1);

namespace Laser\Core\Content\Test\ImportExport;

use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Laser\Core\Framework\DataAbstractionLayer\Search\AggregationResult\AggregationResultCollection;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Laser\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;
use Laser\Core\Framework\DataAbstractionLayer\Write\CloneBehavior;
use Laser\Core\Framework\Event\NestedEventCollection;
use Laser\Core\Framework\Log\Package;

/**
 * @internal can only be used in test setups where bypass finals is activated
 */
#[Package('system-settings')]
class MockRepository extends EntityRepository
{
    public $createCalls = 0;

    public $updateCalls = 0;

    public $upsertCalls = 0;

    public function __construct(private readonly EntityDefinition $definition)
    {
    }

    public function getDefinition(): EntityDefinition
    {
        return $this->definition;
    }

    public function aggregate(Criteria $criteria, Context $context): AggregationResultCollection
    {
        throw new \Error('MockRepository->aggregate: Not implemented');
    }

    public function searchIds(Criteria $criteria, Context $context): IdSearchResult
    {
        throw new \Error('MockRepository->searchIds: Not implemented');
    }

    public function clone(string $id, Context $context, ?string $newId = null, ?CloneBehavior $behavior = null): EntityWrittenContainerEvent
    {
        throw new \Error('MockRepository->clone: Not implemented');
    }

    public function search(Criteria $criteria, Context $context): EntitySearchResult
    {
        throw new \Error('MockRepository->search: Not implemented');
    }

    public function update(array $data, Context $context): EntityWrittenContainerEvent
    {
        ++$this->updateCalls;

        return new EntityWrittenContainerEvent($context, new NestedEventCollection(), []);
    }

    public function upsert(array $data, Context $context): EntityWrittenContainerEvent
    {
        ++$this->upsertCalls;

        return new EntityWrittenContainerEvent($context, new NestedEventCollection(), []);
    }

    public function create(array $data, Context $context): EntityWrittenContainerEvent
    {
        ++$this->createCalls;

        return new EntityWrittenContainerEvent($context, new NestedEventCollection(), []);
    }

    public function delete(array $ids, Context $context): EntityWrittenContainerEvent
    {
        throw new \Error('MockRepository->delete: Not implemented');
    }

    public function createVersion(string $id, Context $context, ?string $name = null, ?string $versionId = null): string
    {
        throw new \Error('MockRepository->createVersion: Not implemented');
    }

    public function merge(string $versionId, Context $context): void
    {
        throw new \Error('MockRepository->merge: Not implemented');
    }
}
