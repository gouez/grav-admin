<?php declare(strict_types=1);

namespace Laser\Core\Migration\V6_3;

use Doctrine\DBAL\Connection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
#[Package('core')]
class Migration1583756864FixDeliveryForeignKey extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1583756864;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('ALTER TABLE `order_delivery` DROP FOREIGN KEY `fk.order_delivery.shipping_order_address_id`');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
