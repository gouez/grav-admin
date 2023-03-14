<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\DataAbstractionLayer\FieldSerializer;

use PHPUnit\Framework\TestCase;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Laser\Core\Framework\DataAbstractionLayer\Field\ConfigJsonField;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Laser\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Laser\Core\Framework\DataAbstractionLayer\FieldSerializer\JsonFieldSerializer;
use Laser\Core\Framework\DataAbstractionLayer\Write\Command\WriteCommandQueue;
use Laser\Core\Framework\DataAbstractionLayer\Write\DataStack\KeyValuePair;
use Laser\Core\Framework\DataAbstractionLayer\Write\EntityExistence;
use Laser\Core\Framework\DataAbstractionLayer\Write\WriteContext;
use Laser\Core\Framework\DataAbstractionLayer\Write\WriteParameterBag;
use Laser\Core\Framework\Test\DataAbstractionLayer\Field\DataAbstractionLayerFieldTestBehaviour;
use Laser\Core\Framework\Test\DataAbstractionLayer\Field\TestDefinition\JsonDefinition;
use Laser\Core\Framework\Test\TestCaseBase\CacheTestBehaviour;
use Laser\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;
use Laser\Core\Framework\Validation\WriteConstraintViolationException;

/**
 * @internal
 */
class JsonFieldSerializerTest extends TestCase
{
    use KernelTestBehaviour;
    use CacheTestBehaviour;
    use DataAbstractionLayerFieldTestBehaviour;

    /**
     * @var JsonFieldSerializer
     */
    private $serializer;

    /**
     * @var ConfigJsonField
     */
    private JsonField $field;

    private EntityExistence $existence;

    private WriteParameterBag $parameters;

    protected function setUp(): void
    {
        $this->serializer = $this->getContainer()->get(JsonFieldSerializer::class);
        $this->field = new JsonField('data', 'data');

        $definition = $this->registerDefinition(JsonDefinition::class);
        $this->existence = new EntityExistence($definition->getEntityName(), [], false, false, false, []);

        $this->parameters = new WriteParameterBag(
            $definition,
            WriteContext::createFromContext(Context::createDefaultContext()),
            '',
            new WriteCommandQueue()
        );
    }

    public static function encodeProvider(): array
    {
        return [
            [new JsonField('data', 'data'), ['foo' => 'bar'], JsonFieldSerializer::encodeJson(['foo' => 'bar'])],
            [new JsonField('data', 'data'), ['foo' => 1], JsonFieldSerializer::encodeJson(['foo' => 1])],
            [new JsonField('data', 'data'), ['foo' => 5.3], JsonFieldSerializer::encodeJson(['foo' => 5.3])],
            [new JsonField('data', 'data'), ['foo' => ['bar' => 'baz']], JsonFieldSerializer::encodeJson(['foo' => ['bar' => 'baz']])],

            [new JsonField('data', 'data'), null, null],
            [new JsonField('data', 'data', [], []), null, JsonFieldSerializer::encodeJson([])],

            [new JsonField('data', 'data', [], ['foo' => 'bar']), null, JsonFieldSerializer::encodeJson(['foo' => 'bar'])],
            [new JsonField('data', 'data', [], ['foo' => 1]), null, JsonFieldSerializer::encodeJson(['foo' => 1])],
            [new JsonField('data', 'data', [], ['foo' => 5.3]), null, JsonFieldSerializer::encodeJson(['foo' => 5.3])],
            [new JsonField('data', 'data', [], ['foo' => ['bar' => 'baz']]), null, JsonFieldSerializer::encodeJson(['foo' => ['bar' => 'baz']])],
        ];
    }

    /**
     * @dataProvider encodeProvider
     */
    public function testEncode(JsonField $field, $input, $expected): void
    {
        $field->compile($this->getContainer()->get(DefinitionInstanceRegistry::class));

        $kvPair = new KeyValuePair('password', $input, true);
        $actual = $this->serializer->encode($field, $this->existence, $kvPair, $this->parameters)->current();

        static::assertEquals($expected, $actual);
    }

    public static function decodeProvider(): array
    {
        return [
            [new JsonField('data', 'data'), JsonFieldSerializer::encodeJson(['foo' => 'bar']), ['foo' => 'bar']],

            [new JsonField('data', 'data'), JsonFieldSerializer::encodeJson(['foo' => 1]), ['foo' => 1]],
            [new JsonField('data', 'data'), JsonFieldSerializer::encodeJson(['foo' => 5.3]), ['foo' => 5.3]],
            [new JsonField('data', 'data'), JsonFieldSerializer::encodeJson(['foo' => ['bar' => 'baz']]), ['foo' => ['bar' => 'baz']]],

            [new JsonField('data', 'data'), null, null],
            [new JsonField('data', 'data', [], []), null, []],

            [new JsonField('data', 'data', [], ['foo' => 'bar']), null, ['foo' => 'bar']],
            [new JsonField('data', 'data', [], ['foo' => 1]), null, ['foo' => 1]],
            [new JsonField('data', 'data', [], ['foo' => 5.3]), null, ['foo' => 5.3]],
            [new JsonField('data', 'data', [], ['foo' => ['bar' => 'baz']]), null, ['foo' => ['bar' => 'baz']]],
        ];
    }

    /**
     * @dataProvider decodeProvider
     */
    public function testDecode(JsonField $field, $input, $expected): void
    {
        $field->compile($this->getContainer()->get(DefinitionInstanceRegistry::class));
        $actual = $this->serializer->decode($field, $input);
        static::assertEquals($expected, $actual);
    }

    public function testEmptyValueForRequiredField(): void
    {
        $field = new JsonField('data', 'data');
        $field->compile($this->getContainer()->get(DefinitionInstanceRegistry::class));

        $kvPair = new KeyValuePair('data', [], true);

        $result = $this->serializer->encode($field, $this->existence, $kvPair, $this->parameters)->current();

        static::assertEquals('[]', $result);
    }

    public function testRequiredValidationThrowsError(): void
    {
        $field = (new JsonField('data', 'data'))->addFlags(new ApiAware(), new Required());
        $field->compile($this->getContainer()->get(DefinitionInstanceRegistry::class));

        $kvPair = new KeyValuePair('data', null, true);

        /** @var WriteConstraintViolationException|null $exception */
        $exception = null;

        try {
            $this->serializer->encode($field, $this->existence, $kvPair, $this->parameters)->current();
        } catch (\Throwable $e) {
            $exception = $e;
        }

        static::assertInstanceOf(WriteConstraintViolationException::class, $exception, 'JsonFieldSerializer does not throw violation exception for empty required field.');
        static::assertEquals('/data', $exception->getViolations()->get(0)->getPropertyPath());
    }

    public function testNullValueForNotRequiredField(): void
    {
        $field = new JsonField('data', 'data');
        $field->compile($this->getContainer()->get(DefinitionInstanceRegistry::class));

        $kvPair = new KeyValuePair('data', null, true);

        $result = $this->serializer->encode($field, $this->existence, $kvPair, $this->parameters)->current();

        static::assertNull($result);
    }

    public function testIgnoresInvalidUtf8Characters(): void
    {
        $result = $this->serializer::encodeJson("something\x82 another");

        static::assertEquals('"something another"', $result);
    }
}
