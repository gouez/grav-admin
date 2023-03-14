<?php declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;

#[Package('core')]
class VersionMergeAlreadyLockedException extends LaserHttpException
{
    public function __construct(string $versionId)
    {
        parent::__construct(
            'Merging of version {{ versionId }} is locked, as the merge is already running by another process.',
            ['versionId' => $versionId]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__VERSION_MERGE_ALREADY_LOCKED';
    }
}
