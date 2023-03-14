<?php declare(strict_types=1);

namespace Laser\Core\System\Language\SalesChannel;

use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\Language\LanguageDefinition;
use Laser\Core\System\SalesChannel\Entity\SalesChannelDefinitionInterface;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('system-settings')]
class SalesChannelLanguageDefinition extends LanguageDefinition implements SalesChannelDefinitionInterface
{
    public function processCriteria(Criteria $criteria, SalesChannelContext $context): void
    {
        $criteria->addFilter(new EqualsFilter('language.salesChannels.id', $context->getSalesChannel()->getId()));
    }
}
