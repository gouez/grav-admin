<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\DataAbstractionLayer\Field;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Laser\Core\Checkout\Cart\Price\Struct\ListPrice;
use Laser\Core\Checkout\Cart\Tax\Struct\CalculatedTaxCollection;
use Laser\Core\Checkout\Cart\Tax\Struct\TaxRuleCollection;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\Read\EntityReaderInterface;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Write\EntityWriter;
use Laser\Core\Framework\DataAbstractionLayer\Write\WriteContext;
use Laser\Core\Framework\Struct\ArrayEntity;
use Laser\Core\Framework\Test\DataAbstractionLayer\Version\CalculatedPriceFieldTestDefinition;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Laser\Core\Framework\Test\TestDataCollection;

/**
 * @internal
 */
class CalculatedPriceFieldTest extends TestCase
{
    use IntegrationTestBehaviour;
    use DataAbstractionLayerFieldTestBehaviour;

    public function testListPrice(): void
    {
        $definition = $this->registerDefinition(CalculatedPriceFieldTestDefinition::class);
        $connection = $this->getContainer()->get(Connection::class);

        $connection->rollBack();
        $connection->executeStatement(CalculatedPriceFieldTestDefinition::getCreateTable());
        $connection->beginTransaction();

        $ids = new TestDataCollection();

        $data = [
            'id' => $ids->create('entity'),
            'price' => new CalculatedPrice(
                100,
                100,
                new CalculatedTaxCollection([]),
                new TaxRuleCollection([]),
                1,
                null,
                ListPrice::createFromUnitPrice(100, 200)
            ),
        ];

        $writeContext = WriteContext::createFromContext(Context::createDefaultContext());

        $this->getContainer()->get(EntityWriter::class)
            ->insert($definition, [$data], $writeContext);

        $entity = $this->getContainer()->get(EntityReaderInterface::class)
            ->read($definition, new Criteria([$ids->get('entity')]), Context::createDefaultContext())
            ->get($ids->get('entity'));

        /** @var ArrayEntity $entity */
        static::assertInstanceOf(ArrayEntity::class, $entity);

        $price = $entity->get('price');

        /** @var CalculatedPrice $price */
        static::assertInstanceOf(CalculatedPrice::class, $price);

        static::assertInstanceOf(ListPrice::class, $price->getListPrice());
        static::assertEquals(200, $price->getListPrice()->getPrice());
        static::assertEquals(-100, $price->getListPrice()->getDiscount());
        static::assertEquals(50, $price->getListPrice()->getPercentage());
    }
}
