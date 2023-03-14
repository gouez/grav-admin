<?php declare(strict_types=1);

namespace Laser\Core\Migration\V6_4;

use Doctrine\DBAL\Connection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
#[Package('core')]
class Migration1648031636AddPositionFieldToShippingMethod extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1648031636;
    }

    public function update(Connection $connection): void
    {
        $columns = array_column($connection->fetchAllAssociative('SHOW COLUMNS FROM `shipping_method`'), 'Field');

        // only execute when the column does not exist
        if (!\in_array('position', $columns, true)) {
            $connection->executeStatement('ALTER TABLE `shipping_method` ADD `position` INT(11) NOT NULL DEFAULT 1 AFTER `active`;');
        }
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
