<?php declare(strict_types=1);

namespace Laser\Core\Content\Test\ImportExport\DataAbstractionLayer\Serializer\Entity;

use PHPUnit\Framework\TestCase;
use Laser\Core\Content\ImportExport\DataAbstractionLayer\Serializer\Entity\CountrySerializer;
use Laser\Core\Content\ImportExport\DataAbstractionLayer\Serializer\SerializerRegistry;
use Laser\Core\Content\ImportExport\Struct\Config;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Laser\Core\System\Country\CountryDefinition;

/**
 * @internal
 */
#[Package('system-settings')]
class CountrySerializerTest extends TestCase
{
    use IntegrationTestBehaviour;

    private EntityRepository $countryRepository;

    private CountrySerializer $serializer;

    private string $countryId = '67d89afb684e44eeacd71ba1f59a5ae1';

    public function setUp(): void
    {
        $this->countryRepository = $this->getContainer()->get('country.repository');
        $serializerRegistry = $this->getContainer()->get(SerializerRegistry::class);

        $this->serializer = new CountrySerializer($this->countryRepository);
        $this->serializer->setRegistry($serializerRegistry);
    }

    public function testSimple(): void
    {
        $this->createCountry();

        $config = new Config([], [], []);
        $country = [
            'iso' => 'XX',
        ];

        $serialized = iterator_to_array($this->serializer->serialize($config, $this->countryRepository->getDefinition(), $country));

        $deserialized = iterator_to_array($this->serializer->deserialize($config, $this->countryRepository->getDefinition(), $serialized));

        static::assertSame($this->countryId, $deserialized['id']);
    }

    public function testSupportsOnlyCountry(): void
    {
        $serializer = new CountrySerializer($this->getContainer()->get('country.repository'));

        $definitionRegistry = $this->getContainer()->get(DefinitionInstanceRegistry::class);
        foreach ($definitionRegistry->getDefinitions() as $definition) {
            $entity = $definition->getEntityName();

            if ($entity === CountryDefinition::ENTITY_NAME) {
                static::assertTrue($serializer->supports($entity));
            } else {
                static::assertFalse(
                    $serializer->supports($entity),
                    CountryDefinition::class . ' should not support ' . $entity
                );
            }
        }
    }

    private function createCountry(): void
    {
        $this->countryRepository->upsert([
            [
                'id' => $this->countryId,
                'iso' => 'XX',
                'name' => 'Test',
            ],
        ], Context::createDefaultContext());
    }
}
