<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\Api\ApiDefinition\Generator;

use PHPUnit\Framework\TestCase;
use Laser\Core\Framework\Api\ApiDefinition\Generator\EntitySchemaGenerator;
use Laser\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Laser\Core\Framework\Test\Api\ApiDefinition\EntityDefinition\SimpleDefinition;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;

/**
 * @internal
 */
final class EntitySchemaGeneratorTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function testAllEntriesHaveProtectionHints(): void
    {
        $definitionRegistry = new DefinitionInstanceRegistry(
            $this->getContainer(),
            ['simple' => SimpleDefinition::class],
            ['simple' => 'simple.repository']
        );
        $definitionRegistry->register(new SimpleDefinition(), 'simple');

        $generator = new EntitySchemaGenerator();
        $definitions = $generator->getSchema($definitionRegistry->getDefinitions());

        static::assertNotEmpty($definitions);

        foreach ($definitions as $definition) {
            static::assertArrayHasKey('write-protected', $definition);
            static::assertArrayHasKey('read-protected', $definition);
        }
    }

    public function testNoEntriesHaveBothProtectionHintsTrue(): void
    {
        $definitionRegistry = new DefinitionInstanceRegistry(
            $this->getContainer(),
            ['simple' => SimpleDefinition::class],
            ['simple' => 'simple.repository']
        );
        $definitionRegistry->register(new SimpleDefinition(), 'simple');

        $generator = new EntitySchemaGenerator();
        /** @var array<string, array{entity: string, properties: array<string, mixed>, write-protected: bool, read-protected: bool}> */
        $definitions = $generator->getSchema($definitionRegistry->getDefinitions());

        foreach ($definitions as $definition) {
            static::assertFalse($definition['write-protected'] && $definition['read-protected']);
        }
    }
}
