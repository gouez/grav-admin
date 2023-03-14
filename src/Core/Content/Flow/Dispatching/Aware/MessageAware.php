<?php declare(strict_types=1);

namespace Laser\Core\Content\Flow\Dispatching\Aware;

use Laser\Core\Framework\Event\FlowEventAware;
use Laser\Core\Framework\Log\Package;
use Symfony\Component\Mime\Email;

#[Package('business-ops')]
interface MessageAware extends FlowEventAware
{
    public const MESSAGE = 'message';

    public function getMessage(): Email;
}
