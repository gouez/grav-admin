<?php declare(strict_types=1);

namespace Laser\Core\System\SalesChannel\DataAbstractionLayer;

use Laser\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexingMessage;
use Laser\Core\Framework\Log\Package;

#[Package('sales-channel')]
class SalesChannelIndexingMessage extends EntityIndexingMessage
{
}
