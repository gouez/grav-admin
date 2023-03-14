<?php declare(strict_types=1);

namespace Laser\Core\Content\Sitemap\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;

#[Package('sales-channel')]
class UnknownFileException extends LaserHttpException
{
    public function getErrorCode(): string
    {
        return 'CONTENT__SITEMAP_UNKNOWN_FILE';
    }
}
