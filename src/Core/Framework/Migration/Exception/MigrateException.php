<?php declare(strict_types=1);

namespace Laser\Core\Framework\Migration\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;

#[Package('core')]
class MigrateException extends LaserHttpException
{
    public function __construct(
        string $message,
        \Exception $previous
    ) {
        parent::__construct('Migration error: {{ errorMessage }}', ['errorMessage' => $message], $previous);
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__MIGRATION_ERROR';
    }
}
