<?php declare(strict_types=1);

namespace Laser\Core\Content\Test\ImportExport\Processing\Pipe;

use PHPUnit\Framework\TestCase;
use Laser\Core\Content\ImportExport\DataAbstractionLayer\Serializer\PrimaryKeyResolver;
use Laser\Core\Content\ImportExport\DataAbstractionLayer\Serializer\SerializerRegistry;
use Laser\Core\Content\ImportExport\Processing\Pipe\EntityPipe;
use Laser\Core\Content\ImportExport\Struct\Config;
use Laser\Core\Content\Product\Aggregate\ProductTranslation\ProductTranslationCollection;
use Laser\Core\Content\Product\Aggregate\ProductTranslation\ProductTranslationEntity;
use Laser\Core\Content\Product\ProductDefinition;
use Laser\Core\Content\Product\ProductEntity;
use Laser\Core\Defaults;
use Laser\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;

/**
 * @internal
 */
#[Package('system-settings')]
class EntityPipeTest extends TestCase
{
    use KernelTestBehaviour;

    public function testEntityPipe(): void
    {
        $entityPipe = new EntityPipe(
            $this->getContainer()->get(DefinitionInstanceRegistry::class),
            $this->getContainer()->get(SerializerRegistry::class),
            null,
            null,
            $this->getContainer()->get(PrimaryKeyResolver::class)
        );

        $sourceEntity = ProductDefinition::ENTITY_NAME;
        $config = new Config([], ['sourceEntity' => $sourceEntity], []);
        $id = Uuid::randomHex();

        $product = (new ProductEntity())->assign([
            'id' => $id,
            'stock' => 101,
            'productNumber' => 'P101',
            'active' => true,
            'translations' => new ProductTranslationCollection([
                (new ProductTranslationEntity())->assign([
                    'languageId' => Defaults::LANGUAGE_SYSTEM,
                    'name' => 'test product',
                    '_uniqueIdentifier' => $id . '_' . Defaults::LANGUAGE_SYSTEM,
                ]),
            ]),
        ]);
        $product->setUniqueIdentifier($id);

        $result = iterator_to_array($entityPipe->in($config, $product));

        static::assertSame($product->getId(), $result['id']);
        static::assertSame($product->getTranslations()->first()->getName(), $result['translations']['DEFAULT']['name']);
        static::assertSame((string) $product->getStock(), $result['stock']);
        static::assertSame($product->getProductNumber(), $result['productNumber']);
        static::assertSame('1', $result['active']);

        $result = iterator_to_array($entityPipe->out($config, $result));

        static::assertSame($product->getId(), $result['id']);
        static::assertSame($product->getTranslations()->first()->getName(), $result['translations'][Defaults::LANGUAGE_SYSTEM]['name']);
        static::assertSame($product->getStock(), $result['stock']);
        static::assertSame($product->getProductNumber(), $result['productNumber']);
        static::assertSame($product->getActive(), $result['active']);
    }
}
