<?php declare(strict_types=1);

namespace Laser\Core\Migration\V6_3;

use Doctrine\DBAL\Connection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
#[Package('core')]
class Migration1578648299ReindexSeoUrls extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1578648299;
    }

    public function update(Connection $connection): void
    {
        $this->registerIndexer($connection, 'Swag.SeoUrlIndexer');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
