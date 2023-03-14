<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\DataAbstractionLayer\Field;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\EntitySearcherInterface;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Laser\Core\Framework\DataAbstractionLayer\Write\EntityWriter;
use Laser\Core\Framework\DataAbstractionLayer\Write\EntityWriterInterface;
use Laser\Core\Framework\DataAbstractionLayer\Write\WriteContext;
use Laser\Core\Framework\Test\DataAbstractionLayer\Field\TestDefinition\ConfigJsonDefinition;
use Laser\Core\Framework\Test\TestCaseBase\CacheTestBehaviour;
use Laser\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;

/**
 * @internal
 */
class ConfigJsonFieldTest extends TestCase
{
    use KernelTestBehaviour;
    use CacheTestBehaviour;
    use DataAbstractionLayerFieldTestBehaviour;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var ConfigJsonDefinition
     */
    private EntityDefinition $configJsonDefinition;

    protected function setUp(): void
    {
        $this->connection = $this->getContainer()->get(Connection::class);

        $nullableTable = <<<EOF
DROP TABLE IF EXISTS _test_nullable;
CREATE TABLE `_test_nullable` (
  `id` varbinary(16) NOT NULL,
  `data` json NULL,
  `created_at` DATETIME(3) NOT NULL,
  `updated_at` DATETIME(3) NULL,
  PRIMARY KEY `id` (`id`)
);
EOF;
        $this->connection->executeStatement($nullableTable);
        $this->connection->beginTransaction();
        $this->configJsonDefinition = $this->registerDefinition(ConfigJsonDefinition::class);
    }

    protected function tearDown(): void
    {
        $this->connection->rollBack();
        $this->connection->executeStatement('DROP TABLE `_test_nullable`');
    }

    public function testFilter(): void
    {
        $context = WriteContext::createFromContext(Context::createDefaultContext());

        $stringId = Uuid::randomHex();
        $string = 'random string';

        $objectId = Uuid::randomHex();
        $object = [
            'foo' => 'bar',
        ];

        $data = [
            [
                'id' => $stringId,
                'data' => $string,
            ],
            [
                'id' => $objectId,
                'data' => $object,
            ],
        ];
        $this->getWriter()->insert($this->configJsonDefinition, $data, $context);

        $searcher = $this->getSearcher();
        $context = $context->getContext();

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('data', $string));
        $result = $searcher->search($this->configJsonDefinition, $criteria, $context);

        static::assertCount(1, $result->getIds());
        static::assertEquals([$stringId], $result->getIds());

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('data.foo', 'bar'));
        $result = $searcher->search($this->configJsonDefinition, $criteria, $context);

        static::assertCount(1, $result->getIds());
        static::assertEquals([$objectId], $result->getIds());

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('data', 'not found'));
        $result = $searcher->search($this->configJsonDefinition, $criteria, $context);

        static::assertCount(0, $result->getIds());
    }

    private function getWriter(): EntityWriterInterface
    {
        return $this->getContainer()->get(EntityWriter::class);
    }

    private function getSearcher(): EntitySearcherInterface
    {
        return $this->getContainer()->get(EntitySearcherInterface::class);
    }
}
