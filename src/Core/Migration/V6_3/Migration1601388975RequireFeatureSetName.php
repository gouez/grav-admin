<?php declare(strict_types=1);

namespace Laser\Core\Migration\V6_3;

use Doctrine\DBAL\Connection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
#[Package('core')]
class Migration1601388975RequireFeatureSetName extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1601388975;
    }

    public function update(Connection $connection): void
    {
    }

    public function updateDestructive(Connection $connection): void
    {
        $connection->executeStatement(
            'ALTER TABLE `product_feature_set_translation` MODIFY `name` VARCHAR(255) DEFAULT \'\' NOT NULL;'
        );
    }
}
