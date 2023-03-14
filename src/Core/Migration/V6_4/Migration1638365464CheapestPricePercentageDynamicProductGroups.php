<?php declare(strict_types=1);

namespace Laser\Core\Migration\V6_4;

use Doctrine\DBAL\Connection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
#[Package('core')]
class Migration1638365464CheapestPricePercentageDynamicProductGroups extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1638365464;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('UPDATE product_stream_filter SET field = "cheapestPrice.percentage" WHERE field = "price.percentage"');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
