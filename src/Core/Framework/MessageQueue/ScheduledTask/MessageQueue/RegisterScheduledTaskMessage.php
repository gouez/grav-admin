<?php declare(strict_types=1);

namespace Laser\Core\Framework\MessageQueue\ScheduledTask\MessageQueue;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\MessageQueue\AsyncMessageInterface;

#[Package('core')]
class RegisterScheduledTaskMessage implements AsyncMessageInterface
{
}
