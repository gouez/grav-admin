<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\Api\Serializer;

use PHPUnit\Framework\TestCase;
use Laser\Core\Content\Media\MediaDefinition;
use Laser\Core\Content\Product\ProductDefinition;
use Laser\Core\Framework\Api\Exception\UnsupportedEncoderInputException;
use Laser\Core\Framework\Api\Serializer\JsonEntityEncoder;
use Laser\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Laser\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\Test\Api\Serializer\fixtures\SerializationFixture;
use Laser\Core\Framework\Test\Api\Serializer\fixtures\TestBasicStruct;
use Laser\Core\Framework\Test\Api\Serializer\fixtures\TestBasicWithExtension;
use Laser\Core\Framework\Test\Api\Serializer\fixtures\TestBasicWithToOneRelationship;
use Laser\Core\Framework\Test\Api\Serializer\fixtures\TestCollectionWithToOneRelationship;
use Laser\Core\Framework\Test\DataAbstractionLayer\Field\DataAbstractionLayerFieldTestBehaviour;
use Laser\Core\Framework\Test\DataAbstractionLayer\Field\TestDefinition\AssociationExtension;
use Laser\Core\Framework\Test\DataAbstractionLayer\Field\TestDefinition\ExtendableDefinition;
use Laser\Core\Framework\Test\DataAbstractionLayer\Field\TestDefinition\ExtendedDefinition;
use Laser\Core\Framework\Test\DataAbstractionLayer\Field\TestDefinition\ScalarRuntimeExtension;
use Laser\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;

/**
 * @internal
 */
class JsonSalesChannelEntityEncoderTest extends TestCase
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
        $encoder->encode(new Criteria(), $this->getContainer()->get(ProductDefinition::class), $input, SerializationFixture::SALES_CHANNEL_API_BASE_URL, SerializationFixture::API_VERSION);
    }

    public static function complexStructsProvider(): array
    {
        return [
            [MediaDefinition::class, new TestBasicStruct()],
            [MediaDefinition::class, new TestBasicWithToOneRelationship()],
            [MediaDefinition::class, new TestCollectionWithToOneRelationship()],
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
        $actual = $encoder->encode(new Criteria(), $definition, $fixture->getInput(), SerializationFixture::SALES_CHANNEL_API_BASE_URL, SerializationFixture::API_VERSION);

        $this->assertValues($fixture->getSalesChannelJsonFixtures(), $actual);
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
        $actual = $encoder->encode(new Criteria(), $extendableDefinition, $fixture->getInput(), SerializationFixture::SALES_CHANNEL_API_BASE_URL, SerializationFixture::API_VERSION);
        unset($actual['apiAlias']);

        $this->assertValues($fixture->getSalesChannelJsonFixtures(), $actual);
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
        $actual = $encoder->encode(new Criteria(), $extendableDefinition, $fixture->getInput(), SerializationFixture::SALES_CHANNEL_API_BASE_URL, SerializationFixture::API_VERSION);
        unset($actual['apiAlias']);

        $this->assertValues($fixture->getSalesChannelJsonFixtures(), $actual);
    }
}
