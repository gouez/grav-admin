<?php declare(strict_types=1);

namespace Laser\Core\Content\Test\ImportExport\DataAbstractionLayer\Serializer\Entity;

use PHPUnit\Framework\TestCase;
use Laser\Core\Content\ImportExport\DataAbstractionLayer\Serializer\Entity\SalutationSerializer;
use Laser\Core\Content\ImportExport\DataAbstractionLayer\Serializer\SerializerRegistry;
use Laser\Core\Content\ImportExport\Struct\Config;
use Laser\Core\Defaults;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\System\Salutation\SalutationDefinition;

/**
 * @internal
 */
#[Package('system-settings')]
class SalutationSerializerTest extends TestCase
{
    use KernelTestBehaviour;

    /**
     * @var EntityRepository
     */
    private $salutationRepository;

    private SalutationSerializer $serializer;

    public function setUp(): void
    {
        $this->salutationRepository = $this->getContainer()->get('salutation.repository');
        $serializerRegistry = $this->getContainer()->get(SerializerRegistry::class);

        $this->serializer = new SalutationSerializer($this->salutationRepository);
        $this->serializer->setRegistry($serializerRegistry);
    }

    public function testSimple(): void
    {
        $config = new Config([], [], []);

        $salutation = [
            'id' => Uuid::randomHex(),
            'salutationKey' => 'mrs',
            'translations' => [
                Defaults::LANGUAGE_SYSTEM => [
                    'displayName' => 'Mrs.',
                    'letterName' => 'Dear Mrs.',
                ],
            ],
        ];

        $serialized = iterator_to_array($this->serializer->serialize($config, $this->salutationRepository->getDefinition(), $salutation));

        $deserialized = iterator_to_array($this->serializer->deserialize($config, $this->salutationRepository->getDefinition(), $serialized));

        $expectedTranslations = $salutation['translations'][Defaults::LANGUAGE_SYSTEM];
        $actualTranslations = $deserialized['translations'][Defaults::LANGUAGE_SYSTEM];
        unset($salutation['translations'], $deserialized['translations']);

        static::assertEquals($salutation, $deserialized);
        static::assertEquals($expectedTranslations, $actualTranslations);
    }

    public function testDeserializeOnlySalutationKey(): void
    {
        $config = new Config([], [], []);

        $salutation = [
            'salutationKey' => 'mrs',
        ];

        $deserialized = iterator_to_array($this->serializer->deserialize($config, $this->salutationRepository->getDefinition(), $salutation));

        static::assertSame($salutation['salutationKey'], $deserialized['salutationKey']);
        static::assertArrayHasKey('id', $deserialized);

        $criteria = (new Criteria())->addFilter(new EqualsFilter('salutationKey', 'mrs'));
        $salutationId = $this->salutationRepository->searchIds($criteria, Context::createDefaultContext())->firstId();

        static::assertSame($salutationId, $deserialized['id']);
    }

    public function testUsesNotSpecifiedAsFallback(): void
    {
        $config = new Config([], [], []);

        $salutation = [
            'salutationKey' => 'unknown',
        ];

        $deserialized = iterator_to_array($this->serializer->deserialize($config, $this->salutationRepository->getDefinition(), $salutation));

        static::assertArrayNotHasKey('salutationKey', $deserialized);
        static::assertArrayHasKey('id', $deserialized);

        $criteria = (new Criteria())->addFilter(new EqualsFilter('salutationKey', 'not_specified'));
        $salutationId = $this->salutationRepository->searchIds($criteria, Context::createDefaultContext())->firstId();

        static::assertSame($salutationId, $deserialized['id']);
    }

    public function testSupportsOnlySalutation(): void
    {
        $serializer = new SalutationSerializer($this->getContainer()->get('salutation.repository'));

        $definitionRegistry = $this->getContainer()->get(DefinitionInstanceRegistry::class);
        foreach ($definitionRegistry->getDefinitions() as $definition) {
            $entity = $definition->getEntityName();

            if ($entity === SalutationDefinition::ENTITY_NAME) {
                static::assertTrue($serializer->supports($entity));
            } else {
                static::assertFalse(
                    $serializer->supports($entity),
                    SalutationDefinition::class . ' should not support ' . $entity
                );
            }
        }
    }
}
