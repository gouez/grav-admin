<?php declare(strict_types=1);

namespace Laser\Core\Content\Test\Product\ProductFeatureSet;

use PHPUnit\Framework\TestCase;
use Laser\Core\Content\Product\Aggregate\ProductFeatureSetTranslation\ProductFeatureSetTranslationCollection;
use Laser\Core\Content\Product\Aggregate\ProductFeatureSetTranslation\ProductFeatureSetTranslationDefinition;
use Laser\Core\Content\Product\Aggregate\ProductFeatureSetTranslation\ProductFeatureSetTranslationEntity;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;

/**
 * @internal
 */
#[Package('inventory')]
class ProductFeatureSetTranslationEntityTest extends TestCase
{
    use KernelTestBehaviour;

    public function testEntityDefinitionExists(): void
    {
        static::assertInstanceOf(
            ProductFeatureSetTranslationDefinition::class,
            new ProductFeatureSetTranslationDefinition()
        );
    }

    /**
     * @dataProvider definitionMethodProvider
     */
    public function testEntityDefinitionIsComplete(string $method, string $returnValue): void
    {
        $definition = $this->getContainer()->get(ProductFeatureSetTranslationDefinition::class);

        static::assertTrue(method_exists($definition, $method));
        static::assertEquals($returnValue, $definition->$method());
    }

    /**
     * @testWith    ["name"]
     *              ["description"]
     */
    public function testDefinitionFieldsAreComplete(string $field): void
    {
        $definition = $this->getContainer()->get(ProductFeatureSetTranslationDefinition::class);

        static::assertTrue($definition->getFields()->has($field));
    }

    public function testEntityExists(): void
    {
        static::assertInstanceOf(
            ProductFeatureSetTranslationEntity::class,
            new ProductFeatureSetTranslationEntity()
        );
    }

    /**
     * @testWith    ["getProductFeatureSetId"]
     *              ["getName"]
     *              ["getDescription"]
     *              ["getProductFeatureSet"]
     */
    public function testEntityIsComplete(string $method): void
    {
        static::assertTrue(method_exists(ProductFeatureSetTranslationEntity::class, $method));
    }

    public function testCollectionExists(): void
    {
        static::assertInstanceOf(
            ProductFeatureSetTranslationCollection::class,
            new ProductFeatureSetTranslationCollection()
        );
    }

    public function testRepositoryIsWorking(): void
    {
        static::assertInstanceOf(EntityRepository::class, $this->getContainer()->get('product_feature_set_translation.repository'));
    }

    public static function definitionMethodProvider(): array
    {
        return [
            [
                'getEntityName',
                'product_feature_set_translation',
            ],
            [
                'getCollectionClass',
                ProductFeatureSetTranslationCollection::class,
            ],
            [
                'getEntityClass',
                ProductFeatureSetTranslationEntity::class,
            ],
        ];
    }
}
