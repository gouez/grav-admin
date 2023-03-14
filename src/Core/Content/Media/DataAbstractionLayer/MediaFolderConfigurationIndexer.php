<?php declare(strict_types=1);

namespace Laser\Core\Content\Media\DataAbstractionLayer;

use Doctrine\DBAL\Connection;
use Laser\Core\Content\Media\Aggregate\MediaFolderConfiguration\MediaFolderConfigurationCollection;
use Laser\Core\Content\Media\Aggregate\MediaFolderConfiguration\MediaFolderConfigurationDefinition;
use Laser\Core\Content\Media\Event\MediaFolderConfigurationIndexerEvent;
use Laser\Core\Framework\DataAbstractionLayer\Dbal\Common\IteratorFactory;
use Laser\Core\Framework\DataAbstractionLayer\Doctrine\RetryableQuery;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Laser\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexer;
use Laser\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexingMessage;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Plugin\Exception\DecorationPatternException;
use Laser\Core\Framework\Uuid\Uuid;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Package('content')]
class MediaFolderConfigurationIndexer extends EntityIndexer
{
    /**
     * @internal
     */
    public function __construct(
        private readonly IteratorFactory $iteratorFactory,
        private readonly EntityRepository $repository,
        private readonly Connection $connection,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function getName(): string
    {
        return 'media_folder_configuration.indexer';
    }

    public function iterate(?array $offset): ?EntityIndexingMessage
    {
        $iterator = $this->iteratorFactory->createIterator($this->repository->getDefinition(), $offset);

        $ids = $iterator->fetch();

        if (empty($ids)) {
            return null;
        }

        return new MediaIndexingMessage(array_values($ids), $iterator->getOffset());
    }

    public function update(EntityWrittenContainerEvent $event): ?EntityIndexingMessage
    {
        $updates = $event->getPrimaryKeys(MediaFolderConfigurationDefinition::ENTITY_NAME);

        if (empty($updates)) {
            return null;
        }

        return new MediaIndexingMessage(array_values($updates), null, $event->getContext());
    }

    public function handle(EntityIndexingMessage $message): void
    {
        $ids = $message->getData();
        $ids = array_unique(array_filter($ids));
        if (empty($ids)) {
            return;
        }

        $criteria = new Criteria();
        $criteria->addAssociation('mediaThumbnailSizes');
        $criteria->setIds($ids);

        $context = $message->getContext();

        /** @var MediaFolderConfigurationCollection $configs */
        $configs = $this->repository->search($criteria, $context);

        $update = new RetryableQuery(
            $this->connection,
            $this->connection->prepare('UPDATE media_folder_configuration SET media_thumbnail_sizes_ro = :media_thumbnail_sizes_ro WHERE id = :id')
        );

        foreach ($configs as $config) {
            $update->execute([
                'media_thumbnail_sizes_ro' => serialize($config->getMediaThumbnailSizes()),
                'id' => Uuid::fromHexToBytes($config->getId()),
            ]);
        }

        $this->eventDispatcher->dispatch(new MediaFolderConfigurationIndexerEvent($ids, $context, $message->getSkip()));
    }

    public function getTotal(): int
    {
        return $this->iteratorFactory->createIterator($this->repository->getDefinition())->fetchCount();
    }

    public function getDecorated(): EntityIndexer
    {
        throw new DecorationPatternException(static::class);
    }
}
