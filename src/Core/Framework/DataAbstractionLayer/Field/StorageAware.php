<?php declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer\Field;

use Laser\Core\Framework\Log\Package;

#[Package('core')]
interface StorageAware
{
    public function getStorageName(): string;
}
