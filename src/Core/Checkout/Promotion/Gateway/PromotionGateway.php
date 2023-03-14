<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Promotion\Gateway;

use Laser\Core\Checkout\Promotion\PromotionCollection;
use Laser\Core\Checkout\Promotion\PromotionEntity;
use Laser\Core\Framework\DataAbstractionLayer\EntityCollection;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('checkout')]
class PromotionGateway implements PromotionGatewayInterface
{
    /**
     * @internal
     */
    public function __construct(private readonly EntityRepository $promotionRepository)
    {
    }

    /**
     * Gets a list of promotions for the provided criteria and
     * sales channel context.
     *
     * @return EntityCollection<PromotionEntity>
     */
    public function get(Criteria $criteria, SalesChannelContext $context): EntityCollection
    {
        $criteria->setTitle('cart::promotion');
        $criteria->addSorting(
            new FieldSorting('priority', FieldSorting::DESCENDING)
        );

        /** @var PromotionCollection $entities */
        $entities = $this->promotionRepository->search($criteria, $context->getContext())->getEntities();

        return $entities;
    }
}
