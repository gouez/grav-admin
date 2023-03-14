<?php declare(strict_types=1);

namespace Laser\Core\Content\Seo\SeoUrl\SalesChannel;

use Laser\Core\Content\Seo\SeoUrl\SeoUrlDefinition;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\Entity\SalesChannelDefinitionInterface;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('sales-channel')]
class SalesChannelSeoUrlDefinition extends SeoUrlDefinition implements SalesChannelDefinitionInterface
{
    public function processCriteria(Criteria $criteria, SalesChannelContext $context): void
    {
        $criteria->addFilter(new EqualsFilter('languageId', $context->getContext()->getLanguageId()));
        $criteria->addFilter(new MultiFilter(MultiFilter::CONNECTION_OR, [
            new EqualsFilter('salesChannelId', $context->getSalesChannel()->getId()),
            new EqualsFilter('salesChannelId', null),
        ]));
        $criteria->addFilter(new EqualsFilter('isCanonical', true));
        $criteria->addFilter(new EqualsFilter('isDeleted', false));
    }
}
