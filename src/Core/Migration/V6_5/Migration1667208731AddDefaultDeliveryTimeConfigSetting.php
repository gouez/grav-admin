<?php declare(strict_types=1);

namespace Laser\Core\Migration\V6_5;

use Doctrine\DBAL\Connection;
use Laser\Core\Defaults;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Migration\MigrationStep;
use Laser\Core\Framework\Uuid\Uuid;

/**
 * @internal
 */
#[Package('core')]
class Migration1667208731AddDefaultDeliveryTimeConfigSetting extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1667208731;
    }

    public function update(Connection $connection): void
    {
        if ($this->checkIfSettingExists($connection)) {
            return;
        }

        $this->insertSettingValue($connection);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }

    private function insertSettingValue(Connection $connection): void
    {
        $query = 'INSERT INTO system_config (`id`, `configuration_key`, `configuration_value`, `created_at`)
                  VALUES (:id, :configKey, :configValue, :createdAt);';

        $connection->executeStatement($query, [
            'id' => Uuid::randomBytes(),
            'configKey' => 'core.cart.showDeliveryTime',
            'configValue' => '{"_value": true}',
            'createdAt' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);
    }

    private function checkIfSettingExists(Connection $connection): bool
    {
        $selectSql = 'SELECT id FROM system_config WHERE configuration_key = "core.cart.showDeliveryTime"';

        $result = $connection->fetchOne($selectSql);

        if (!\is_string($result)) {
            return false;
        }

        return true;
    }
}
