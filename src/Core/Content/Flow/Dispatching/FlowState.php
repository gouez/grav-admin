<?php declare(strict_types=1);

namespace Laser\Core\Content\Flow\Dispatching;

use Laser\Core\Content\Flow\Dispatching\Struct\Sequence;
use Laser\Core\Framework\Log\Package;

#[Package('business-ops')]
class FlowState
{
    public string $flowId;

    public bool $stop = false;

    public Sequence $currentSequence;

    public bool $delayed = false;

    public function getSequenceId(): string
    {
        return $this->currentSequence->sequenceId;
    }
}
