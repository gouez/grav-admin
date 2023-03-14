<?php declare(strict_types=1);

namespace Laser\Core\System\SalesChannel\Entity;

use Laser\Core\Framework\DataAbstractionLayer\Entity;
use Laser\Core\Framework\DataAbstractionLayer\EntityCollection;
use Laser\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Laser\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent;
use Laser\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEventFactory;
use Laser\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent;
use Laser\Core\Framework\DataAbstractionLayer\Exception\InconsistentCriteriaIdsException;
use Laser\Core\Framework\DataAbstractionLayer\Field\AssociationField;
use Laser\Core\Framework\DataAbstractionLayer\Read\EntityReaderInterface;
use Laser\Core\Framework\DataAbstractionLayer\RepositorySearchDetector;
use Laser\Core\Framework\DataAbstractionLayer\Search\AggregationResult\AggregationResultCollection;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\EntityAggregatorInterface;
use Laser\Core\Framework\DataAbstractionLayer\Search\EntitySearcherInterface;
use Laser\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Laser\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\ArrayEntity;
use Laser\Core\System\SalesChannel\Event\SalesChannelProcessCriteriaEvent;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @final
 */
#[Package('sales-channel')]
class SalesChannelRepository
{
    /**
     * @internal
     */
    public function __construct(
        private readonly EntityDefinition $definition,
        private readonly EntityReaderInterface $reader,
        private readonly EntitySearcherInterface $searcher,
        private readonly EntityAggregatorInterface $aggregator,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly EntityLoadedEventFactory $eventFactory
    ) {
    }

    /**
     * @throws InconsistentCriteriaIdsException
     */
    public function search(Criteria $criteria, SalesChannelContext $salesChannelContext): EntitySearchResult
    {
        $criteria = clone $criteria;

        $this->processCriteria($criteria, $salesChannelContext);

        $aggregations = null;
        if ($criteria->getAggregations()) {
            $aggregations = $this->aggregate($criteria, $salesChannelContext);
        }
        if (!RepositorySearchDetector::isSearchRequired($this->definition, $criteria)) {
            $entities = $this->read($criteria, $salesChannelContext);

            return new EntitySearchResult($this->definition->getEntityName(), $entities->count(), $entities, $aggregations, $criteria, $salesChannelContext->getContext());
        }

        $ids = $this->doSearch($criteria, $salesChannelContext);

        if (empty($ids->getIds())) {
            /** @var EntityCollection<Entity> $collection */
            $collection = $this->definition->getCollectionClass();

            return new EntitySearchResult($this->definition->getEntityName(), $ids->getTotal(), new $collection(), $aggregations, $criteria, $salesChannelContext->getContext());
        }

        $readCriteria = $criteria->cloneForRead($ids->getIds());

        $entities = $this->read($readCriteria, $salesChannelContext);

        $search = $ids->getData();

        /** @var Entity $element */
        foreach ($entities as $element) {
            if (!\array_key_exists($element->getUniqueIdentifier(), $search)) {
                continue;
            }

            $data = $search[$element->getUniqueIdentifier()];
            unset($data['id']);

            if (empty($data)) {
                continue;
            }

            $element->addExtension('search', new ArrayEntity($data));
        }

        $result = new EntitySearchResult($this->definition->getEntityName(), $ids->getTotal(), $entities, $aggregations, $criteria, $salesChannelContext->getContext());
        $result->addState(...$ids->getStates());

        $event = new EntitySearchResultLoadedEvent($this->definition, $result);
        $this->eventDispatcher->dispatch($event, $event->getName());

        $event = new SalesChannelEntitySearchResultLoadedEvent($this->definition, $result, $salesChannelContext);
        $this->eventDispatcher->dispatch($event, $event->getName());

        return $result;
    }

    public function aggregate(Criteria $criteria, SalesChannelContext $salesChannelContext): AggregationResultCollection
    {
        $criteria = clone $criteria;

        $this->processCriteria($criteria, $salesChannelContext);

        $result = $this->aggregator->aggregate($this->definition, $criteria, $salesChannelContext->getContext());

        $event = new EntityAggregationResultLoadedEvent($this->definition, $result, $salesChannelContext->getContext());
        $this->eventDispatcher->dispatch($event, $event->getName());

        return $result;
    }

    public function searchIds(Criteria $criteria, SalesChannelContext $salesChannelContext): IdSearchResult
    {
        $criteria = clone $criteria;

        $this->processCriteria($criteria, $salesChannelContext);

        return $this->doSearch($criteria, $salesChannelContext);
    }

    /**
     * @return EntityCollection<Entity>
     */
    private function read(Criteria $criteria, SalesChannelContext $salesChannelContext): EntityCollection
    {
        $criteria = clone $criteria;

        $entities = $this->reader->read($this->definition, $criteria, $salesChannelContext->getContext());

        if ($criteria->getFields() === []) {
            $events = $this->eventFactory->createForSalesChannel($entities->getElements(), $salesChannelContext);
        } else {
            $events = $this->eventFactory->createPartialForSalesChannel($entities->getElements(), $salesChannelContext);
        }

        foreach ($events as $event) {
            $this->eventDispatcher->dispatch($event);
        }

        return $entities;
    }

    private function doSearch(Criteria $criteria, SalesChannelContext $salesChannelContext): IdSearchResult
    {
        $result = $this->searcher->search($this->definition, $criteria, $salesChannelContext->getContext());

        $event = new SalesChannelEntityIdSearchResultLoadedEvent($this->definition, $result, $salesChannelContext);
        $this->eventDispatcher->dispatch($event, $event->getName());

        return $result;
    }

    private function processCriteria(Criteria $topCriteria, SalesChannelContext $salesChannelContext): void
    {
        if (!$this->definition instanceof SalesChannelDefinitionInterface) {
            return;
        }

        $queue = [
            ['definition' => $this->definition, 'criteria' => $topCriteria],
        ];

        $maxCount = 100;

        $processed = [];

        // process all associations breadth-first
        while (!empty($queue) && --$maxCount > 0) {
            $cur = array_shift($queue);

            /** @var EntityDefinition $definition */
            $definition = $cur['definition'];
            $criteria = $cur['criteria'];

            if (isset($processed[$definition::class])) {
                continue;
            }

            if ($definition instanceof SalesChannelDefinitionInterface) {
                $definition->processCriteria($criteria, $salesChannelContext);

                $eventName = \sprintf('sales_channel.%s.process.criteria', $definition->getEntityName());
                $event = new SalesChannelProcessCriteriaEvent($criteria, $salesChannelContext);

                $this->eventDispatcher->dispatch($event, $eventName);
            }

            $processed[$definition::class] = true;

            foreach ($criteria->getAssociations() as $associationName => $associationCriteria) {
                // find definition
                $field = $definition->getField($associationName);
                if (!$field instanceof AssociationField) {
                    continue;
                }

                $referenceDefinition = $field->getReferenceDefinition();
                $queue[] = ['definition' => $referenceDefinition, 'criteria' => $associationCriteria];
            }
        }
    }
}
