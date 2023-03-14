<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\Api\Serializer;

use PHPUnit\Framework\TestCase;
use Laser\Core\Content\Media\Aggregate\MediaFolder\MediaFolderDefinition;
use Laser\Core\Content\Media\MediaDefinition;
use Laser\Core\Content\Product\ProductDefinition;
use Laser\Core\Content\Rule\RuleDefinition;
use Laser\Core\Framework\Api\Exception\UnsupportedEncoderInputException;
use Laser\Core\Framework\Api\Serializer\JsonEntityEncoder;
use Laser\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Laser\Core\Framework\DataAbstractionLayer\Entity;
use Laser\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\Test\Api\Serializer\fixtures\SerializationFixture;
use Laser\Core\Framework\Test\Api\Serializer\fixtures\TestBasicStruct;
use Laser\Core\Framework\Test\Api\Serializer\fixtures\TestBasicWithExtension;
use Laser\Core\Framework\Test\Api\Serializer\fixtures\TestBasicWithToManyRelationships;
use Laser\Core\Framework\Test\Api\Serializer\fixtures\TestBasicWithToOneRelationship;
use Laser\Core\Framework\Test\Api\Serializer\fixtures\TestCollectionWithSelfReference;
use Laser\Core\Framework\Test\Api\Serializer\fixtures\TestCollectionWithToOneRelationship;
use Laser\Core\Framework\Test\Api\Serializer\fixtures\TestInternalFieldsAreFiltered;
use Laser\Core\Framework\Test\Api\Serializer\fixtures\TestMainResourceShouldNotBeInIncluded;
use Laser\Core\Framework\Test\DataAbstractionLayer\Field\DataAbstractionLayerFieldTestBehaviour;
use Laser\Core\Framework\Test\DataAbstractionLayer\Field\TestDefinition\AssociationExtension;
use Laser\Core\Framework\Test\DataAbstractionLayer\Field\TestDefinition\CustomFieldTestDefinition;
use Laser\Core\Framework\Test\DataAbstractionLayer\Field\TestDefinition\ExtendableDefinition;
use Laser\Core\Framework\Test\DataAbstractionLayer\Field\TestDefinition\ExtendedDefinition;
use Laser\Core\Framework\Test\DataAbstractionLayer\Field\TestDefinition\ScalarRuntimeExtension;
use Laser\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;
use Laser\Core\System\User\UserDefinition;

/**
 * @internal
 */
class JsonEntityEncoderTest extends TestCase
{
    use KernelTestBehaviour;
    use DataAbstractionLayerFieldTestBehaviour;
    use AssertValuesTrait;

    public static function emptyInputProvider(): array
    {
        return [
            [null],
            ['string'],
            [1],
            [false],
            [new \DateTime()],
            [1.1],
        ];
    }

    /**
     * @dataProvider emptyInputProvider
     */
    public function testEncodeWithEmptyInput($input): void
    {
        $this->expectException(UnsupportedEncoderInputException::class);
        $encoder = $this->getContainer()->get(JsonEntityEncoder::class);
        $encoder->encode(new Criteria(), $this->getContainer()->get(ProductDefinition::class), $input, SerializationFixture::API_BASE_URL);
    }

    public static function complexStructsProvider(): array
    {
        return [
            [MediaDefinition::class, new TestBasicStruct()],
            [UserDefinition::class, new TestBasicWithToManyRelationships()],
            [MediaDefinition::class, new TestBasicWithToOneRelationship()],
            [MediaFolderDefinition::class, new TestCollectionWithSelfReference()],
            [MediaDefinition::class, new TestCollectionWithToOneRelationship()],
            [RuleDefinition::class, new TestInternalFieldsAreFiltered()],
            [UserDefinition::class, new TestMainResourceShouldNotBeInIncluded()],
        ];
    }

    /**
     * @dataProvider complexStructsProvider
     */
    public function testEncodeComplexStructs(string $definitionClass, SerializationFixture $fixture): void
    {
        /** @var EntityDefinition $definition */
        $definition = $this->getContainer()->get($definitionClass);
        $encoder = $this->getContainer()->get(JsonEntityEncoder::class);
        $actual = $encoder->encode(new Criteria(), $definition, $fixture->getInput(), SerializationFixture::API_BASE_URL);

        $this->assertValues($fixture->getAdminJsonFixtures(), $actual);
    }

    /**
     * Not possible with dataprovider
     * as we have to manipulate the container, but the dataprovider run before all tests
     */
    public function testEncodeStructWithExtension(): void
    {
        $this->registerDefinition(ExtendableDefinition::class, ExtendedDefinition::class);
        $extendableDefinition = new ExtendableDefinition();
        $extendableDefinition->addExtension(new AssociationExtension());
        $extendableDefinition->addExtension(new ScalarRuntimeExtension());

        $extendableDefinition->compile($this->getContainer()->get(DefinitionInstanceRegistry::class));
        $fixture = new TestBasicWithExtension();

        $encoder = $this->getContainer()->get(JsonEntityEncoder::class);
        $actual = $encoder->encode(new Criteria(), $extendableDefinition, $fixture->getInput(), SerializationFixture::API_BASE_URL);

        unset($actual['apiAlias']);
        static::assertEquals($fixture->getAdminJsonFixtures(), $actual);
        $this->assertValues($fixture->getAdminJsonFixtures(), $actual);
    }

    /**
     * Not possible with dataprovider
     * as we have to manipulate the container, but the dataprovider run before all tests
     */
    public function testEncodeStructWithToManyExtension(): void
    {
        $this->registerDefinition(ExtendableDefinition::class, ExtendedDefinition::class);
        $extendableDefinition = new ExtendableDefinition();
        $extendableDefinition->addExtension(new AssociationExtension());

        $extendableDefinition->compile($this->getContainer()->get(DefinitionInstanceRegistry::class));
        $fixture = new TestBasicWithExtension();

        $encoder = $this->getContainer()->get(JsonEntityEncoder::class);
        $actual = $encoder->encode(new Criteria(), $extendableDefinition, $fixture->getInput(), SerializationFixture::API_BASE_URL);

        unset($actual['apiAlias']);
        static::assertEquals($fixture->getAdminJsonFixtures(), $actual);
    }

    /**
     * @dataProvider customFieldsProvider
     */
    public function testCustomFields(array $input, array $output): void
    {
        $encoder = $this->getContainer()->get(JsonEntityEncoder::class);

        $definition = new CustomFieldTestDefinition();
        $definition->compile($this->getContainer()->get(DefinitionInstanceRegistry::class));
        $struct = new Entity();
        $struct->assign($input);

        $actual = $encoder->encode(new Criteria(), $definition, $struct, SerializationFixture::API_BASE_URL);

        static::assertEquals($output, array_intersect_key($output, $actual));
    }

    public static function customFieldsProvider(): iterable
    {
        yield 'Custom field null' => [
            [
                'customFields' => null,
            ],
            [
                'customFields' => null,
            ],
        ];

        yield 'Custom field with empty array' => [
            [
                'customFields' => [],
            ],
            [
                'customFields' => new \stdClass(),
            ],
        ];

        yield 'Custom field with values' => [
            [
                'customFields' => ['bla'],
            ],
            [
                'customFields' => ['bla'],
            ],
        ];

        // translated

        yield 'Custom field translated null' => [
            [
                'translated' => [
                    'customFields' => null,
                ],
            ],
            [
                'translated' => [
                    'customFields' => null,
                ],
            ],
        ];

        yield 'Custom field translated with empty array' => [
            [
                'translated' => [
                    'customFields' => [],
                ],
            ],
            [
                'translated' => [
                    'customFields' => new \stdClass(),
                ],
            ],
        ];

        yield 'Custom field translated with values' => [
            [
                'translated' => [
                    'customFields' => ['bla'],
                ],
            ],
            [
                'translated' => [
                    'customFields' => ['bla'],
                ],
            ],
        ];
    }
}
