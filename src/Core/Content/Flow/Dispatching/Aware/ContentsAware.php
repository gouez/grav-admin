<?php declare(strict_types=1);

namespace Laser\Core\Content\Flow\Dispatching\Aware;

use Laser\Core\Framework\Event\FlowEventAware;
use Laser\Core\Framework\Log\Package;

#[Package('business-ops')]
interface ContentsAware extends FlowEventAware
{
    public const CONTENTS = 'contents';

    /**
     * @return array<string, mixed>
     */
    public function getContents(): array;
}
