<?php declare(strict_types=1);

namespace Laser\Core\Migration\V6_3;

use Doctrine\DBAL\Connection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
#[Package('core')]
class Migration1570629862ClearCategoryBreadcrumbs extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1570629862;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('UPDATE `category_translation` SET `breadcrumb` = NULL');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
