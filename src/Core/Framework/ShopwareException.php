<?php declare(strict_types=1);

namespace Laser\Core\Framework;

use Laser\Core\Framework\Log\Package;

#[Package('core')]
interface LaserException extends \Throwable
{
    public function getErrorCode(): string;

    /**
     * @return array<string|int, mixed|null>
     */
    public function getParameters(): array;
}
