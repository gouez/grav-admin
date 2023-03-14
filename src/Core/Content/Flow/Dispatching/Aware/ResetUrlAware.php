<?php declare(strict_types=1);

namespace Laser\Core\Content\Flow\Dispatching\Aware;

use Laser\Core\Framework\Event\FlowEventAware;
use Laser\Core\Framework\Log\Package;

#[Package('business-ops')]
interface ResetUrlAware extends FlowEventAware
{
    public const RESET_URL = 'resetUrl';

    public function getResetUrl(): string;
}
