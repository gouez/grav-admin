<?php declare(strict_types=1);

namespace Laser\Core\Framework\Migration\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;

#[Package('core')]
class InvalidMigrationClassException extends LaserHttpException
{
    public function __construct(
        string $class,
        string $path
    ) {
        parent::__construct(
            'Unable to load migration {{ class }} at path {{ path }}',
            ['class' => $class, 'path' => $path]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__INVALID_MIGRATION';
    }
}
