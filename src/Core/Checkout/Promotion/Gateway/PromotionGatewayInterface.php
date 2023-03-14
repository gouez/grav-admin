<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Promotion\Gateway;

use Laser\Core\Checkout\Promotion\PromotionEntity;
use Laser\Core\Framework\DataAbstractionLayer\EntityCollection;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('checkout')]
interface PromotionGatewayInterface
{
    /**
     * Gets a list of promotions for the provided criteria and
     * sales channel context.
     *
     * @return EntityCollection<PromotionEntity>
     */
    public function get(Criteria $criteria, SalesChannelContext $context): EntityCollection;
}
