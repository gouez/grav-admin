<?php declare(strict_types=1);

namespace Laser\Core\Migration\V6_4;

use Doctrine\DBAL\Connection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
#[Package('core')]
class Migration1651118773UpdateZipCodeOfTableCustomerAddressToNullable extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1651118773;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
        ALTER TABLE `customer_address`
        MODIFY COLUMN `zipcode` varchar(50) NULL
        SQL;

        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
