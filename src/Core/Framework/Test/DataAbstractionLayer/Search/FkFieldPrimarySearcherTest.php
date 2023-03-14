<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\DataAbstractionLayer\Search;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Laser\Core\Content\Product\Aggregate\ProductTranslation\ProductTranslationCollection;
use Laser\Core\Defaults;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEventFactory;
use Laser\Core\Framework\DataAbstractionLayer\Read\EntityReaderInterface;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\EntityAggregatorInterface;
use Laser\Core\Framework\DataAbstractionLayer\Search\EntitySearcherInterface;
use Laser\Core\Framework\DataAbstractionLayer\VersionManager;
use Laser\Core\Framework\Struct\ArrayEntity;
use Laser\Core\Framework\Test\DataAbstractionLayer\Search\Definition\FkFieldPrimaryTestDefinition;
use Laser\Core\Framework\Test\DataAbstractionLayer\Search\Definition\MultiFkFieldPrimaryTestDefinition;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Laser\Core\Framework\Test\TestCaseBase\KernelLifecycleManager;
use Laser\Core\Framework\Uuid\Uuid;

/**
 * @internal
 */
class FkFieldPrimarySearcherTest extends TestCase
{
    use IntegrationTestBehaviour;

    private EntityRepository $productRepository;

    private string $productId;

    public static function tearDownAfterClass(): void
    {
        KernelLifecycleManager::getKernel()->getContainer()->get(Connection::class)->executeStatement('DROP TABLE IF EXISTS multi_fk_field_primary');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $connection = $this->getContainer()->get(Connection::class);
        $connection->rollBack();
        $connection->executeStatement('DROP TABLE IF EXISTS `fk_field_primary`');
        $connection->beginTransaction();
    }

    public function testSearchByPrimaryFkKey(): void
    {
        $this->addPrimaryFkField();

        $definition = new FkFieldPrimaryTestDefinition();
        $this->productRepository = $this->getContainer()->get('product.repository');
        $this->productId = Uuid::randomHex();

        $this->productRepository->create(
            [
                [
                    'id' => $this->productId,
                    'productNumber' => Uuid::randomHex(),
                    'stock' => 1,
                    'name' => 'Test',
                    'tax' => ['name' => 'test', 'taxRate' => 5],
                    'price' => [['currencyId' => Defaults::CURRENCY, 'gross' => 10, 'net' => 5, 'linked' => false]],
                ],
            ],
            Context::createDefaultContext()
        );

        /** @var EntityRepository $fkFieldPrimaryRepository */
        $fkFieldPrimaryRepository = $this->getContainer()->get($definition->getEntityName() . '.repository');

        $fkFieldPrimaryRepository->create(
            [
                [
                    'productId' => $this->productId,
                    'name' => 'TestPrimary',
                ],
            ],
            Context::createDefaultContext()
        );

        $criteria = new Criteria([$this->productId]);
        /** @var EntityRepository $fkFieldPrimaryRepository */
        $fkFieldPrimaryRepository = $this->getContainer()->get('fk_field_primary.repository');
        /** @var array<string, ArrayEntity> $fkFieldPrimaryTupel */
        $fkFieldPrimaryTupel = $fkFieldPrimaryRepository->search($criteria, Context::createDefaultContext())->getElements();
        static::assertArrayHasKey($this->productId, $fkFieldPrimaryTupel);
        static::assertTrue($fkFieldPrimaryTupel[$this->productId]->has('name'));
        static::assertEquals('TestPrimary', $fkFieldPrimaryTupel[$this->productId]->get('name'));
    }

    public function testSearchByMultiPrimaryFkKey(): void
    {
        $this->addMultiPrimaryFkField();

        /** @var EntityRepository $multiPrimaryRepository */
        $multiPrimaryRepository = $this->getContainer()->get('multi_fk_field_primary.repository');
        $firstId = Uuid::randomHex();
        $secondId = Uuid::randomHex();

        $multiPrimaryRepository->create(
            [
                [
                    'firstId' => $firstId,
                    'secondId' => $secondId,
                ],
            ],
            Context::createDefaultContext()
        );

        $criteria = new Criteria([['firstId' => $firstId, 'secondId' => $secondId]]);
        $multiFkFieldPrimaryTupel = $multiPrimaryRepository->search($criteria, Context::createDefaultContext());
        $key = $firstId . '-' . $secondId;
        static::assertArrayHasKey($key, $multiFkFieldPrimaryTupel->getElements());
        static::assertEquals($firstId, $multiFkFieldPrimaryTupel->getElements()[$key]->get('firstId'));
    }

    public function testSearchForTranslation(): void
    {
        $this->productRepository = $this->getContainer()->get('product.repository');
        $this->productId = Uuid::randomHex();

        $this->productRepository->create(
            [
                [
                    'id' => $this->productId,
                    'productNumber' => Uuid::randomHex(),
                    'stock' => 1,
                    'name' => 'Test',
                    'tax' => ['name' => 'test', 'taxRate' => 5],
                    'price' => [['currencyId' => Defaults::CURRENCY, 'gross' => 10, 'net' => 5, 'linked' => false]],
                ],
            ],
            Context::createDefaultContext()
        );

        $criteria = new Criteria([['productId' => $this->productId, 'languageId' => Defaults::LANGUAGE_SYSTEM]]);

        $productTranslationRepository = $this->getContainer()->get('product_translation.repository');
        /** @var ProductTranslationCollection $productTranslation */
        $productTranslation = $productTranslationRepository->search($criteria, Context::createDefaultContext());

        $key = $this->productId . '-' . Defaults::LANGUAGE_SYSTEM;
        static::assertArrayHasKey($key, $productTranslation->getElements());
        static::assertEquals('Test', $productTranslation->getElements()[$key]->getName());
    }

    private function addPrimaryFkField(): void
    {
        $connection = $this->getContainer()->get(Connection::class);
        $connection->rollBack();
        $connection->executeStatement('DROP TABLE IF EXISTS `fk_field_primary`');
        $connection->executeStatement('
            CREATE TABLE `fk_field_primary` (
              `product_id` BINARY(16) NOT NULL PRIMARY KEY,
              `name` varchar(255) DEFAULT NULL,
              `created_at` DATETIME(3) NOT NULL,
              `updated_at` DATETIME(3) NULL
        )');

        $definition = new FkFieldPrimaryTestDefinition();

        if (!$this->getContainer()->has($definition->getEntityName() . '.repository')) {
            $repository = new EntityRepository(
                $definition,
                $this->getContainer()->get(EntityReaderInterface::class),
                $this->getContainer()->get(VersionManager::class),
                $this->getContainer()->get(EntitySearcherInterface::class),
                $this->getContainer()->get(EntityAggregatorInterface::class),
                $this->getContainer()->get('event_dispatcher'),
                $this->getContainer()->get(EntityLoadedEventFactory::class)
            );

            $this->getContainer()->set($definition->getEntityName() . '.repository', $repository);
            $this->getContainer()->get(DefinitionInstanceRegistry::class)->register($definition);
            $definition->compile($this->getContainer()->get(DefinitionInstanceRegistry::class));
        }

        $connection->beginTransaction();
    }

    private function addMultiPrimaryFkField(): void
    {
        $connection = $this->getContainer()->get(Connection::class);
        $connection->rollBack();
        $connection->executeStatement('DROP TABLE IF EXISTS `multi_fk_field_primary`');
        $connection->executeStatement(
            '
                CREATE TABLE `multi_fk_field_primary` (
                  `first_id` BINARY(16) NOT NULL,
                  `second_id` BINARY(16) NOT NULL,
                  `created_at` DATETIME(3) NOT NULL,
                  `updated_at` DATETIME(3) NULL,
                  PRIMARY KEY (`first_id`, `second_id`)
            )'
        );

        $definition = new MultiFkFieldPrimaryTestDefinition();

        if (!$this->getContainer()->has($definition->getEntityName() . '.repository')) {
            $repository = new EntityRepository(
                $definition,
                $this->getContainer()->get(EntityReaderInterface::class),
                $this->getContainer()->get(VersionManager::class),
                $this->getContainer()->get(EntitySearcherInterface::class),
                $this->getContainer()->get(EntityAggregatorInterface::class),
                $this->getContainer()->get('event_dispatcher'),
                $this->getContainer()->get(EntityLoadedEventFactory::class)
            );

            $this->getContainer()->set($definition->getEntityName() . '.repository', $repository);
            $this->getContainer()->get(DefinitionInstanceRegistry::class)->register($definition);
            $definition->compile($this->getContainer()->get(DefinitionInstanceRegistry::class));
        }

        $connection->beginTransaction();
    }
}
