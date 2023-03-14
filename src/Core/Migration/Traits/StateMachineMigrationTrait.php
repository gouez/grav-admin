<?php declare(strict_types=1);

namespace Laser\Core\Migration\Traits;

use Doctrine\DBAL\Connection;
use Laser\Core\Framework\Log\Package;

#[Package('core')]
trait StateMachineMigrationTrait
{
    private function import(StateMachineMigration $migration, Connection $connection): StateMachineMigration
    {
        return (new StateMachineMigrationImporter($connection))->importStateMachine($migration);
    }
}
