<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\CustomField;

use PHPUnit\Framework\TestCase;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Laser\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use Laser\Core\Framework\DataAbstractionLayer\Field\FloatField;
use Laser\Core\Framework\DataAbstractionLayer\Field\IntField;
use Laser\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Laser\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\System\CustomField\CustomFieldService;
use Laser\Core\System\CustomField\CustomFieldTypes;

/**
 * @internal
 */
class CustomFieldServiceTest extends TestCase
{
    use IntegrationTestBehaviour;

    /**
     * @var EntityRepository
     */
    private $attributeRepository;

    /**
     * @var CustomFieldService
     */
    private $attributeService;

    public function setUp(): void
    {
        $this->attributeRepository = $this->getContainer()->get('custom_field.repository');
        $this->attributeService = $this->getContainer()->get(CustomFieldService::class);
    }

    public static function attributeFieldTestProvider(): array
    {
        return [
            [
                CustomFieldTypes::BOOL, BoolField::class,
                CustomFieldTypes::DATETIME, DateTimeField::class,
                CustomFieldTypes::FLOAT, FloatField::class,
                CustomFieldTypes::HTML, LongTextField::class,
                CustomFieldTypes::INT, IntField::class,
                CustomFieldTypes::JSON, JsonField::class,
                CustomFieldTypes::TEXT, LongTextField::class,
            ],
        ];
    }

    /**
     * @dataProvider attributeFieldTestProvider
     */
    public function testGetCustomFieldField(string $attributeType, string $expectedFieldClass): void
    {
        $attribute = [
            'name' => 'test_attr',
            'type' => $attributeType,
        ];
        $this->attributeRepository->create([$attribute], Context::createDefaultContext());

        static::assertInstanceOf(
            $expectedFieldClass,
            $this->attributeService->getCustomField('test_attr')
        );
    }

    public function testOnlyGetActive(): void
    {
        $id = Uuid::randomHex();
        $this->attributeRepository->upsert([[
            'id' => $id,
            'name' => 'test_attr',
            'active' => false,
            'type' => CustomFieldTypes::TEXT,
        ]], Context::createDefaultContext());

        $actual = $this->attributeService->getCustomField('test_attr');
        static::assertNull($actual);

        $this->attributeRepository->upsert([[
            'id' => $id,
            'active' => true,
        ]], Context::createDefaultContext());
        $actual = $this->attributeService->getCustomField('test_attr');
        static::assertNotNull($actual);
    }
}
