<?php declare(strict_types=1);

namespace Laser\Core\Maintenance\SalesChannel\Command;

use Laser\Core\Framework\Log\Package;
use Symfony\Component\Console\Attribute\AsCommand;

/**
 * @internal should be used over the CLI only
 */
#[AsCommand(
    name: 'sales-channel:maintenance:disable',
    description: 'Disable maintenance mode for a sales channel',
)]
#[Package('core')]
class SalesChannelMaintenanceDisableCommand extends SalesChannelMaintenanceEnableCommand
{
    protected $setMaintenanceMode = false;
}
