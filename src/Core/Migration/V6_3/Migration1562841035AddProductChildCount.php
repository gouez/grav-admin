<?php declare(strict_types=1);

namespace Laser\Core\Migration\V6_3;

use Doctrine\DBAL\Connection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
#[Package('core')]
class Migration1562841035AddProductChildCount extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1562841035;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('ALTER TABLE `product` ADD `child_count` INT(11)');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
