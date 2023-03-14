<?php declare(strict_types=1);

namespace Laser\Core\Migration\V6_4;

use Doctrine\DBAL\Connection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
#[Package('core')]
class Migration1620201616AddUpdatedAtToCart extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1620201616;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('ALTER TABLE `cart` ADD COLUMN `updated_at` DATETIME(3) NULL;');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
