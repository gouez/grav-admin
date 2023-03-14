<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\DataAbstractionLayer\FieldSerializer;

use PHPUnit\Framework\TestCase;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\Field\ConfigJsonField;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Laser\Core\Framework\DataAbstractionLayer\FieldSerializer\ConfigJsonFieldSerializer;
use Laser\Core\Framework\DataAbstractionLayer\Write\Command\WriteCommandQueue;
use Laser\Core\Framework\DataAbstractionLayer\Write\DataStack\KeyValuePair;
use Laser\Core\Framework\DataAbstractionLayer\Write\EntityExistence;
use Laser\Core\Framework\DataAbstractionLayer\Write\WriteContext;
use Laser\Core\Framework\DataAbstractionLayer\Write\WriteParameterBag;
use Laser\Core\Framework\Test\DataAbstractionLayer\Field\DataAbstractionLayerFieldTestBehaviour;
use Laser\Core\Framework\Test\DataAbstractionLayer\Field\TestDefinition\JsonDefinition;
use Laser\Core\Framework\Test\TestCaseBase\CacheTestBehaviour;
use Laser\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;

/**
 * @internal
 */
class ConfigJsonFieldSerializerTest extends TestCase
{
    use KernelTestBehaviour;
    use CacheTestBehaviour;
    use DataAbstractionLayerFieldTestBehaviour;

    /**
     * @var ConfigJsonFieldSerializer
     */
    private $serializer;

    private ConfigJsonField $field;

    private EntityExistence $existence;

    private WriteParameterBag $parameters;

    protected function setUp(): void
    {
        $this->serializer = $this->getContainer()->get(ConfigJsonFieldSerializer::class);
        $this->field = new ConfigJsonField('data', 'data');
        $this->field->addFlags(new ApiAware(), new Required());

        $definition = $this->registerDefinition(JsonDefinition::class);
        $this->existence = new EntityExistence($definition->getEntityName(), [], false, false, false, []);

        $this->parameters = new WriteParameterBag(
            $definition,
            WriteContext::createFromContext(Context::createDefaultContext()),
            '',
            new WriteCommandQueue()
        );
    }

    public static function serializerProvider(): array
    {
        return [
            [['string']],
            [[11234]],
            [[11234.123243]],
            [[
                [
                    'foo' => 'sadfsadf',
                    'bar' => [
                        'a' => 1234,
                    ],
                ],
            ]],
            [[
                [1, 2, 3],
            ]],
            [[null]],
            [[false]],
            [[0]],
            [['']],
        ];
    }

    /**
     * @dataProvider serializerProvider
     */
    public function testSerializer($input): void
    {
        $kvPair = new KeyValuePair('password', $input, true);
        $encoded = $this->serializer->encode($this->field, $this->existence, $kvPair, $this->parameters)->current();
        $decoded = $this->serializer->decode($this->field, $encoded);

        static::assertEquals($input, $decoded, 'Output should be equal to the input');
    }
}
