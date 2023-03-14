<?php declare(strict_types=1);

namespace Laser\Core\Framework\Api\Context;

use Laser\Core\Framework\Log\Package;

#[Package('core')]
class SystemSource implements ContextSource
{
    public string $type = 'system';
}
