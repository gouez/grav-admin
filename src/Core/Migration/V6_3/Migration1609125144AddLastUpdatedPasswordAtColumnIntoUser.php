<?php declare(strict_types=1);

namespace Laser\Core\Migration\V6_3;

use Doctrine\DBAL\Connection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
#[Package('core')]
class Migration1609125144AddLastUpdatedPasswordAtColumnIntoUser extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1609125144;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            ALTER TABLE `user` ADD COLUMN `last_updated_password_at` DATETIME(3) NULL AFTER `store_token`;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
