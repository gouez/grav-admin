<?php declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer\FieldSerializer;

use Laser\Core\Content\Product\DataAbstractionLayer\VariantListingConfig;
use Laser\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Laser\Core\Framework\DataAbstractionLayer\Exception\InvalidSerializerFieldException;
use Laser\Core\Framework\DataAbstractionLayer\Field\Field;
use Laser\Core\Framework\DataAbstractionLayer\Field\VariantListingConfigField;
use Laser\Core\Framework\DataAbstractionLayer\Write\DataStack\KeyValuePair;
use Laser\Core\Framework\DataAbstractionLayer\Write\EntityExistence;
use Laser\Core\Framework\DataAbstractionLayer\Write\WriteParameterBag;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Uuid\Uuid;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Optional;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @internal
 */
#[Package('core')]
class VariantListingConfigFieldSerializer extends AbstractFieldSerializer
{
    /**
     * @internal
     */
    public function __construct(
        DefinitionInstanceRegistry $definitionRegistry,
        ValidatorInterface $validator
    ) {
        parent::__construct($validator, $definitionRegistry);
    }

    public function encode(
        Field $field,
        EntityExistence $existence,
        KeyValuePair $data,
        WriteParameterBag $parameters
    ): \Generator {
        if (!$field instanceof VariantListingConfigField) {
            throw new InvalidSerializerFieldException(VariantListingConfigField::class, $field);
        }

        $this->validateIfNeeded($field, $existence, $data, $parameters);

        $value = $data->getValue();

        $displayParent = isset($value['displayParent']) ? (int) $value['displayParent'] : null;
        $mainVariantId = isset($value['mainVariantId']) ? Uuid::fromHexToBytes($value['mainVariantId']) : null;
        $configuratorGroupConfig = isset($value['configuratorGroupConfig']) ? \json_encode($value['configuratorGroupConfig'], \JSON_THROW_ON_ERROR) : null;

        yield 'display_parent' => $displayParent;
        yield 'main_variant_id' => $mainVariantId;
        yield 'configurator_group_config' => $configuratorGroupConfig;
    }

    public function decode(Field $field, mixed $value): ?VariantListingConfig
    {
        if ($value === null) {
            return null;
        }

        if (\is_string($value)) {
            $value = json_decode($value, true, 512, \JSON_THROW_ON_ERROR);
        }

        return new VariantListingConfig(
            $value['displayParent'] !== null ? (bool) $value['displayParent'] : null,
            $value['mainVariantId'],
            $value['configuratorGroupConfig']
        );
    }

    protected function getConstraints(Field $field): array
    {
        return [
            new Collection([
                'allowExtraFields' => true,
                'allowMissingFields' => true,
                'fields' => [
                    'displayParent' => [new Type('boolean')],
                    'mainVariantId' => [new \Laser\Core\Framework\Validation\Constraint\Uuid()],
                    'configuratorGroupConfig' => [
                        new Optional(
                            new Collection([
                                'allowExtraFields' => true,
                                'allowMissingFields' => true,
                                'fields' => [
                                    'id' => [new NotBlank(), new \Laser\Core\Framework\Validation\Constraint\Uuid()],
                                    'representation' => [new NotBlank(), new Type('string')],
                                    'expressionForListings' => [new NotBlank(), new Type('boolean')],
                                ],
                            ])
                        ),
                    ],
                ],
            ]),
        ];
    }
}
