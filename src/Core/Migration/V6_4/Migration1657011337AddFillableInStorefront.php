<?php declare(strict_types=1);

namespace Laser\Core\Migration\V6_4;

use Doctrine\DBAL\Connection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
#[Package('core')]
class Migration1657011337AddFillableInStorefront extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1657011337;
    }

    public function update(Connection $connection): void
    {
        $field = $connection->fetchOne(
            'SHOW COLUMNS FROM `custom_field` WHERE `Field` LIKE :column;',
            ['column' => 'allow_customer_write']
        );

        if (!empty($field)) {
            return;
        }

        $connection->executeStatement('ALTER TABLE `custom_field` ADD `allow_customer_write` tinyint default 0 NOT NULL');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
