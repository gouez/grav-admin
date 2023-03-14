<?php declare(strict_types=1);

namespace Laser\Core\Migration\V6_5;

use Doctrine\DBAL\Connection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
#[Package('content')]
class Migration1662533751AddCustomEntityTypeIdToCategory extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1662533751;
    }

    public function update(Connection $connection): void
    {
        if (!$this->columnExists($connection, 'category', 'custom_entity_type_id')) {
            $connection->executeStatement(
                'ALTER TABLE `category` ADD `custom_entity_type_id` BINARY(16) NULL,
                ADD CONSTRAINT `fk.category.custom_entity_type_id` FOREIGN KEY (`custom_entity_type_id`)
                    REFERENCES `custom_entity` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;'
            );
        }
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
