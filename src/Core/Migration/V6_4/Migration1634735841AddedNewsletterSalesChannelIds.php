<?php declare(strict_types=1);

namespace Laser\Core\Migration\V6_4;

use Doctrine\DBAL\Connection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
#[Package('core')]
class Migration1634735841AddedNewsletterSalesChannelIds extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1634735841;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('ALTER TABLE `customer` ADD `newsletter_sales_channel_ids` json NULL AFTER `last_login`');
        $this->registerIndexer($connection, 'newsletter_recipient.indexer');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
