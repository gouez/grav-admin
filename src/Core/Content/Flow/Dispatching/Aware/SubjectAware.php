<?php declare(strict_types=1);

namespace Laser\Core\Content\Flow\Dispatching\Aware;

use Laser\Core\Framework\Event\FlowEventAware;
use Laser\Core\Framework\Log\Package;

#[Package('business-ops')]
interface SubjectAware extends FlowEventAware
{
    public const SUBJECT = 'subject';

    public function getSubject(): string;
}
