<?php declare(strict_types=1);

namespace Laser\Core\Framework\Api\Sync;

use Doctrine\DBAL\ConnectionException;
use Laser\Core\Framework\Api\Exception\InvalidSyncOperationException;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Log\Package;

#[Package('core')]
interface SyncServiceInterface
{
    /**
     * @param SyncOperation[] $operations
     *
     * @throws ConnectionException
     * @throws InvalidSyncOperationException
     */
    public function sync(array $operations, Context $context, SyncBehavior $behavior): SyncResult;
}
