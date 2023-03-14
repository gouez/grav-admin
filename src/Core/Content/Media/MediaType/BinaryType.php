<?php declare(strict_types=1);

namespace Laser\Core\Content\Media\MediaType;

use Laser\Core\Framework\Log\Package;

#[Package('content')]
class BinaryType extends MediaType
{
    protected $name = 'BINARY';
}
