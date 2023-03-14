<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\DataAbstractionLayer\FieldSerializer;

use PHPUnit\Framework\TestCase;
use Laser\Core\Content\Product\ProductDefinition;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Laser\Core\Framework\DataAbstractionLayer\FieldSerializer\PriceFieldSerializer;
use Laser\Core\Framework\DataAbstractionLayer\FieldSerializer\TranslatedFieldSerializer;
use Laser\Core\Framework\DataAbstractionLayer\Write\Command\WriteCommandQueue;
use Laser\Core\Framework\DataAbstractionLayer\Write\WriteContext;
use Laser\Core\Framework\DataAbstractionLayer\Write\WriteParameterBag;
use Laser\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;

/**
 * @internal
 */
class TranslatedFieldSerializerTest extends TestCase
{
    use KernelTestBehaviour;

    /**
     * @var PriceFieldSerializer
     */
    protected $serializer;

    protected WriteContext $writeContext;

    public function setUp(): void
    {
        $this->serializer = $this->getContainer()->get(TranslatedFieldSerializer::class);
        $this->writeContext = WriteContext::createFromContext(Context::createDefaultContext());
    }

    public function testNormalizeNullData(): void
    {
        $data = $this->normalize(['description' => null]);

        static::assertEquals([
            'description' => null,
            'translations' => [
                $this->writeContext->getContext()->getLanguageId() => [
                    'description' => null,
                ],
            ],
        ], $data);
    }

    public function testNormalizeStringData(): void
    {
        $data = $this->normalize(['description' => 'abc']);

        static::assertEquals([
            'description' => 'abc',
            'translations' => [
                $this->writeContext->getContext()->getLanguageId() => [
                    'description' => 'abc',
                ],
            ],
        ], $data);
    }

    public function testNormalizeArrayData(): void
    {
        $languageId = $this->writeContext->getContext()->getLanguageId();

        $data = $this->normalize([
            'description' => [
                $languageId => 'abc',
            ],
        ]);

        static::assertEquals([
            'description' => [
                $languageId => 'abc',
            ],
            'translations' => [
                $languageId => [
                    'description' => 'abc',
                ],
            ],
        ], $data);
    }

    private function normalize(array $data): array
    {
        $field = new TranslatedField('description');
        $bag = new WriteParameterBag(
            $this->getContainer()->get(ProductDefinition::class),
            $this->writeContext,
            '',
            new WriteCommandQueue()
        );

        return $this->serializer->normalize($field, $data, $bag);
    }
}
