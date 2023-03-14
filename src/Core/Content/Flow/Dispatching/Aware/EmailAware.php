<?php declare(strict_types=1);

namespace Laser\Core\Content\Flow\Dispatching\Aware;

use Laser\Core\Framework\Event\FlowEventAware;
use Laser\Core\Framework\Log\Package;

#[Package('business-ops')]
interface EmailAware extends FlowEventAware
{
    public const EMAIL = 'email';

    public function getEmail(): string;
}
