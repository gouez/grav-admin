<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\DataAbstractionLayer\Field;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Laser\Core\Defaults;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEventFactory;
use Laser\Core\Framework\DataAbstractionLayer\Read\EntityReaderInterface;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\EntityAggregatorInterface;
use Laser\Core\Framework\DataAbstractionLayer\Search\EntitySearcherInterface;
use Laser\Core\Framework\DataAbstractionLayer\VersionManager;
use Laser\Core\Framework\Struct\ArrayEntity;
use Laser\Core\Framework\Test\DataAbstractionLayer\Field\TestDefinition\DateTimeDefinition;
use Laser\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;

/**
 * @internal
 */
class CreateAtAndUpdatedAtFieldTest extends TestCase
{
    use KernelTestBehaviour;
    use DataAbstractionLayerFieldTestBehaviour;

    /**
     * @var Connection
     */
    private $connection;

    private EntityRepository $repo;

    protected function setUp(): void
    {
        $definition = $this->registerDefinition(DateTimeDefinition::class);
        $this->connection = $this->getContainer()->get(Connection::class);
        $this->repo = new EntityRepository(
            $definition,
            $this->getContainer()->get(EntityReaderInterface::class),
            $this->getContainer()->get(VersionManager::class),
            $this->getContainer()->get(EntitySearcherInterface::class),
            $this->getContainer()->get(EntityAggregatorInterface::class),
            $this->getContainer()->get('event_dispatcher'),
            $this->getContainer()->get(EntityLoadedEventFactory::class)
        );

        $nullableTable = <<<EOF
DROP TABLE IF EXISTS `date_time_test`;
CREATE TABLE IF NOT EXISTS `date_time_test` (
  `id` varbinary(16) NOT NULL,
  `name` varchar(500) NULL,
  `created_at` datetime(3) NOT NULL,
  `updated_at` datetime(3) NULL,
  PRIMARY KEY `id` (`id`)
);
EOF;
        $this->connection->executeStatement($nullableTable);
        $this->connection->beginTransaction();
    }

    public function tearDown(): void
    {
        $this->connection->rollBack();
        $this->connection->executeStatement('DROP TABLE `date_time_test`');
    }

    public function testCreatedAtDefinedAutomatically(): void
    {
        $id = Uuid::randomHex();

        $data = [
            'id' => $id,
        ];

        $context = Context::createDefaultContext();
        $this->repo->create([$data], $context);

        $entities = $this->repo->search(new Criteria([$id]), $context);

        static::assertTrue($entities->has($id));

        /** @var ArrayEntity $entity */
        $entity = $entities->get($id);

        static::assertNotNull($entity->get('createdAt'));
        static::assertInstanceOf(\DateTimeInterface::class, $entity->get('createdAt'));
        static::assertNull($entity->get('updatedAt'));
    }

    public function testICanProvideCreatedAt(): void
    {
        $id = Uuid::randomHex();

        $date = new \DateTime('2000-01-01 12:12:12.990');

        $data = [
            'id' => $id,
            'createdAt' => $date,
        ];

        $context = Context::createDefaultContext();
        $this->repo->create([$data], $context);

        $entities = $this->repo->search(new Criteria([$id]), $context);

        static::assertTrue($entities->has($id));

        /** @var ArrayEntity $entity */
        $entity = $entities->get($id);

        static::assertNotNull($entity->get('createdAt'));
        static::assertInstanceOf(\DateTimeInterface::class, $entity->get('createdAt'));

        static::assertEquals(
            $date->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            $entity->get('createdAt')->format(Defaults::STORAGE_DATE_TIME_FORMAT)
        );
    }

    public function testCreatedAtWithNullWillBeOverwritten(): void
    {
        $id = Uuid::randomHex();

        $data = [
            'id' => $id,
            'createdAt' => null,
        ];

        $context = Context::createDefaultContext();
        $this->repo->create([$data], $context);

        $entities = $this->repo->search(new Criteria([$id]), $context);

        static::assertTrue($entities->has($id));

        /** @var ArrayEntity $entity */
        $entity = $entities->get($id);

        static::assertNotNull($entity->get('createdAt'));
        static::assertInstanceOf(\DateTimeInterface::class, $entity->get('createdAt'));
    }

    public function testUpdatedAtWillBeSetAutomatically(): void
    {
        $id = Uuid::randomHex();

        $data = ['id' => $id];

        $context = Context::createDefaultContext();
        $this->repo->create([$data], $context);

        $entities = $this->repo->search(new Criteria([$id]), $context);

        static::assertTrue($entities->has($id));
        /** @var ArrayEntity $entity */
        $entity = $entities->get($id);
        static::assertNull($entity->get('updatedAt'));

        $data = ['id' => $id, 'name' => 'updated'];

        $context = Context::createDefaultContext();
        $this->repo->update([$data], $context);
        $entities = $this->repo->search(new Criteria([$id]), $context);

        static::assertTrue($entities->has($id));
        /** @var ArrayEntity $entity */
        $entity = $entities->get($id);
        static::assertNotNull($entity->get('updatedAt'));
    }

    public function testUpdatedAtWithNullWorks(): void
    {
        $id = Uuid::randomHex();

        $data = ['id' => $id];

        $context = Context::createDefaultContext();
        $this->repo->create([$data], $context);

        $entities = $this->repo->search(new Criteria([$id]), $context);

        static::assertTrue($entities->has($id));
        /** @var ArrayEntity $entity */
        $entity = $entities->get($id);
        static::assertNull($entity->get('updatedAt'));

        $data = ['id' => $id, 'name' => 'updated', 'updatedAt' => null];

        $context = Context::createDefaultContext();
        $this->repo->update([$data], $context);
        $entities = $this->repo->search(new Criteria([$id]), $context);

        static::assertTrue($entities->has($id));
        /** @var ArrayEntity $entity */
        $entity = $entities->get($id);
        static::assertNotNull($entity->get('updatedAt'));
    }

    public function testUpdatedAtCanNotBeDefined(): void
    {
        $id = Uuid::randomHex();

        $date = new \DateTime('2012-01-01');

        $data = ['id' => $id, 'updatedAt' => $date];

        $context = Context::createDefaultContext();
        $this->repo->create([$data], $context);

        $entities = $this->repo->search(new Criteria([$id]), $context);

        static::assertTrue($entities->has($id));
        /** @var ArrayEntity $entity */
        $entity = $entities->get($id);
        static::assertNull($entity->get('updatedAt'));

        $data = ['id' => $id, 'name' => 'updated', 'updatedAt' => $date];

        $context = Context::createDefaultContext();
        $this->repo->update([$data], $context);
        $entities = $this->repo->search(new Criteria([$id]), $context);

        static::assertTrue($entities->has($id));
        /** @var ArrayEntity $entity */
        $entity = $entities->get($id);
        static::assertNotNull($entity->get('updatedAt'));

        static::assertNotEquals(
            $date->format('Y-m-d'),
            $entity->get('updatedAt')->format('Y-m-d')
        );
    }
}
