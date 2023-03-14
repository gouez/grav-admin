<?php declare(strict_types=1);

namespace Laser\Core\Content\Seo\Entity\Serializer;

use Laser\Core\Content\Seo\Entity\Field\SeoUrlAssociationField;
use Laser\Core\Framework\DataAbstractionLayer\Exception\InvalidSerializerFieldException;
use Laser\Core\Framework\DataAbstractionLayer\Field\Field;
use Laser\Core\Framework\DataAbstractionLayer\FieldSerializer\OneToManyAssociationFieldSerializer;
use Laser\Core\Framework\DataAbstractionLayer\Write\DataStack\KeyValuePair;
use Laser\Core\Framework\DataAbstractionLayer\Write\EntityExistence;
use Laser\Core\Framework\DataAbstractionLayer\Write\WriteParameterBag;
use Laser\Core\Framework\Feature;
use Laser\Core\Framework\Log\Package;

/**
 * @deprecated tag:v6.6.0 - will be removed
 */
#[Package('sales-channel')]
class SeoUrlFieldSerializer extends OneToManyAssociationFieldSerializer
{
    public function encode(Field $field, EntityExistence $existence, KeyValuePair $data, WriteParameterBag $parameters): \Generator
    {
        Feature::triggerDeprecationOrThrow(
            'v6.6.0.0',
            Feature::deprecatedMethodMessage(self::class, __METHOD__, 'v6.6.0.0')
        );

        if (!$field instanceof SeoUrlAssociationField) {
            throw new InvalidSerializerFieldException(SeoUrlAssociationField::class, $field);
        }

        $seoUrls = $data->getValue();
        foreach ($seoUrls as $i => $seoUrl) {
            $seoUrl['routeName'] = $field->getRouteName();
            $seoUrl['isModified'] = true;

            $seoUrls[$i] = $seoUrl;
        }

        $data = new KeyValuePair($data->getKey(), $seoUrls, $data->isRaw());

        return parent::encode($field, $existence, $data, $parameters);
    }
}
