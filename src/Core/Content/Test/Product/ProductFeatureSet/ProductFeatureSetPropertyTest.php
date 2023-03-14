<?php declare(strict_types=1);

namespace Laser\Core\Content\Test\Product\ProductFeatureSet;

use PHPUnit\Framework\TestCase;
use Laser\Core\Content\Product\ProductDefinition;
use Laser\Core\Content\Product\ProductEntity;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;

/**
 * @internal
 */
class ProductFeatureSetPropertyTest extends TestCase
{
    use IntegrationTestBehaviour;
    use ProductFeatureSetFixtures;

    /**
     * @testWith    ["featureSet"]
     */
    public function testDefinitionFieldsAreComplete(string $field): void
    {
        $definition = $this->getContainer()->get(ProductDefinition::class);

        static::assertTrue($definition->getFields()->has($field));
    }

    /**
     * @testWith    ["getFeatureSet"]
     */
    public function testEntityIsComplete(string $method): void
    {
        static::assertTrue(method_exists(ProductEntity::class, $method));
    }

    /**
     * @testWith    ["FeatureSetBasic"]
     *              ["FeatureSetComplete"]
     */
    public function testFeatureSetsCanBeCreated(string $type): void
    {
        $this->getFeatureSetFixture($type);
    }
}
