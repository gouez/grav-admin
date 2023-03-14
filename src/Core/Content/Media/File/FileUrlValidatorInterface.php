<?php declare(strict_types=1);

namespace Laser\Core\Content\Media\File;

use Laser\Core\Framework\Log\Package;

#[Package('content')]
interface FileUrlValidatorInterface
{
    public function isValid(string $source): bool;
}
