<?php declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;

#[Package('core')]
class InconsistentCriteriaIdsException extends LaserHttpException
{
    public function __construct()
    {
        parent::__construct('Inconsistent argument for Criteria. Please filter all invalid values first.');
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__INCONSISTENT_CRITERIA_IDS';
    }
}
