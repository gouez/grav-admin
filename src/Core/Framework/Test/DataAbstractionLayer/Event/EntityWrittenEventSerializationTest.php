<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\DataAbstractionLayer\Event;

use PHPUnit\Framework\TestCase;
use Laser\Core\Content\Product\ProductDefinition;
use Laser\Core\Defaults;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;

/**
 * @internal
 */
class EntityWrittenEventSerializationTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function testEventCanBeSerialized(): void
    {
        $container = $this->writeTestProduct();
        $event = $container->getEventByEntityName(ProductDefinition::ENTITY_NAME);

        $encoded = json_encode($event, \JSON_THROW_ON_ERROR);
        static::assertNotFalse($encoded);
        static::assertJson($encoded);

        $encoded = json_encode($container, \JSON_THROW_ON_ERROR);
        static::assertNotFalse($encoded);
        static::assertJson($encoded);
    }

    private function writeTestProduct(): EntityWrittenContainerEvent
    {
        /** @var EntityRepository $productRepository */
        $productRepository = $this->getContainer()->get('product.repository');

        return $productRepository->create(
            [[
                'id' => Uuid::randomHex(),
                'manufacturer' => [
                    'id' => Uuid::randomHex(),
                    'name' => 'amazing brand',
                ],
                'name' => 'wusel',
                'productNumber' => Uuid::randomHex(),
                'tax' => ['id' => Uuid::randomHex(), 'taxRate' => 19, 'name' => 'tax'],
                'price' => [['currencyId' => Defaults::CURRENCY, 'gross' => 10, 'net' => 12, 'linked' => false]],
                'stock' => 0,
            ]],
            Context::createDefaultContext()
        );
    }
}
