<?php declare(strict_types=1);

namespace Laser\Core\Content\Flow\Dispatching\Aware;

use Laser\Core\Framework\Event\FlowEventAware;
use Laser\Core\Framework\Log\Package;

#[Package('business-ops')]
interface DataAware extends FlowEventAware
{
    public const DATA = 'data';

    /**
     * @return array<string, mixed>
     */
    public function getData(): array;
}
