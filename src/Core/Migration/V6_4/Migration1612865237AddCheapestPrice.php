<?php declare(strict_types=1);

namespace Laser\Core\Migration\V6_4;

use Doctrine\DBAL\Connection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
#[Package('core')]
class Migration1612865237AddCheapestPrice extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1612865237;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('ALTER TABLE `product` ADD `cheapest_price` longtext NULL;');
        $connection->executeStatement('ALTER TABLE `product` ADD `cheapest_price_accessor` longtext NULL;');
    }

    public function updateDestructive(Connection $connection): void
    {
        // nth
    }
}
