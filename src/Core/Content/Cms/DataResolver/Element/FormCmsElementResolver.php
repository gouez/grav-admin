<?php declare(strict_types=1);

namespace Laser\Core\Content\Cms\DataResolver\Element;

use Laser\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Laser\Core\Content\Cms\DataResolver\CriteriaCollection;
use Laser\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\Salutation\SalesChannel\AbstractSalutationRoute;
use Laser\Core\System\Salutation\SalutationEntity;
use Symfony\Component\HttpFoundation\Request;

#[Package('content')]
class FormCmsElementResolver extends AbstractCmsElementResolver
{
    /**
     * @internal
     */
    public function __construct(private readonly AbstractSalutationRoute $salutationRoute)
    {
    }

    public function getType(): string
    {
        return 'form';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        return null;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $context = $resolverContext->getSalesChannelContext();

        $salutations = $this->salutationRoute->load(new Request(), $context, new Criteria())->getSalutations();

        $salutations->sort(fn (SalutationEntity $a, SalutationEntity $b) => $b->getSalutationKey() <=> $a->getSalutationKey());

        $slot->setData($salutations);
    }
}
