<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Customer\SalesChannel;

use Laser\Core\Checkout\Customer\Exception\CustomerGroupRegistrationConfigurationNotFound;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Plugin\Exception\DecorationPatternException;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\Routing\Annotation\Route;

#[Route(defaults: ['_routeScope' => ['store-api']])]
#[Package('customer-order')]
class CustomerGroupRegistrationSettingsRoute extends AbstractCustomerGroupRegistrationSettingsRoute
{
    /**
     * @internal
     */
    public function __construct(private readonly EntityRepository $customerGroupRepository)
    {
    }

    public function getDecorated(): AbstractCustomerGroupRegistrationSettingsRoute
    {
        throw new DecorationPatternException(self::class);
    }

    #[Route(path: '/store-api/customer-group-registration/config/{customerGroupId}', name: 'store-api.customer-group-registration.config', methods: ['GET'])]
    public function load(string $customerGroupId, SalesChannelContext $context): CustomerGroupRegistrationSettingsRouteResponse
    {
        $criteria = new Criteria([$customerGroupId]);
        $criteria->addFilter(new EqualsFilter('registrationActive', 1));
        $criteria->addFilter(new EqualsFilter('registrationSalesChannels.id', $context->getSalesChannel()->getId()));

        $result = $this->customerGroupRepository->search($criteria, $context->getContext());

        if ($result->getTotal() === 0) {
            throw new CustomerGroupRegistrationConfigurationNotFound($customerGroupId);
        }

        return new CustomerGroupRegistrationSettingsRouteResponse($result->first());
    }
}
