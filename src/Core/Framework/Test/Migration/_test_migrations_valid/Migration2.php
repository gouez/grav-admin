<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\Migration\_test_migrations_valid;

use Doctrine\DBAL\Connection;
use Laser\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
class Migration2 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 2;
    }

    public function update(Connection $connection): void
    {
        //nth
    }

    public function updateDestructive(Connection $connection): void
    {
        //nth
    }
}
