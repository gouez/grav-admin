<?php declare(strict_types=1);

namespace Laser\Core\Content\Flow\Events;

use Laser\Core\Content\Flow\Dispatching\StorableFlow;
use Laser\Core\Content\MailTemplate\MailTemplateEntity;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Event\LaserEvent;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Validation\DataBag\DataBag;

#[Package('business-ops')]
class FlowSendMailActionEvent implements LaserEvent
{
    public function __construct(
        private readonly DataBag $dataBag,
        private readonly MailTemplateEntity $mailTemplate,
        private readonly StorableFlow $flow
    ) {
    }

    public function getContext(): Context
    {
        return $this->flow->getContext();
    }

    public function getDataBag(): DataBag
    {
        return $this->dataBag;
    }

    public function getMailTemplate(): MailTemplateEntity
    {
        return $this->mailTemplate;
    }

    public function getStorableFlow(): StorableFlow
    {
        return $this->flow;
    }
}
