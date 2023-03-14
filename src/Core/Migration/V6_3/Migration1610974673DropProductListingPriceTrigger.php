<?php declare(strict_types=1);

namespace Laser\Core\Migration\V6_3;

use Doctrine\DBAL\Connection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
#[Package('core')]
class Migration1610974673DropProductListingPriceTrigger extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1610974673;
    }

    public function update(Connection $connection): void
    {
        // implement update
    }

    public function updateDestructive(Connection $connection): void
    {
        try {
            $connection->executeStatement(
                'DROP TRIGGER `product_listing_price_update`'
            );
        } catch (\Throwable) {
        }
    }
}
