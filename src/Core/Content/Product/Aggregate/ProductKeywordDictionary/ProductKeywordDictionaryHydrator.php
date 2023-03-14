<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\Aggregate\ProductKeywordDictionary;

use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\Dbal\EntityHydrator;
use Laser\Core\Framework\DataAbstractionLayer\Entity;
use Laser\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Uuid\Uuid;

#[Package('inventory')]
class ProductKeywordDictionaryHydrator extends EntityHydrator
{
    protected function assign(EntityDefinition $definition, Entity $entity, string $root, array $row, Context $context): Entity
    {
        if (isset($row[$root . '.id'])) {
            $entity->id = Uuid::fromBytesToHex($row[$root . '.id']);
        }
        if (isset($row[$root . '.languageId'])) {
            $entity->languageId = Uuid::fromBytesToHex($row[$root . '.languageId']);
        }
        if (isset($row[$root . '.keyword'])) {
            $entity->keyword = $row[$root . '.keyword'];
        }
        if (isset($row[$root . '.reversed'])) {
            $entity->reversed = $row[$root . '.reversed'];
        }
        $entity->language = $this->manyToOne($row, $root, $definition->getField('language'), $context);

        $this->translate($definition, $entity, $row, $root, $context, $definition->getTranslatedFields());
        $this->hydrateFields($definition, $entity, $root, $row, $context, $definition->getExtensionFields());

        return $entity;
    }
}
