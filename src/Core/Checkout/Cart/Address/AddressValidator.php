<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\Address;

use Laser\Core\Checkout\Cart\Address\Error\BillingAddressSalutationMissingError;
use Laser\Core\Checkout\Cart\Address\Error\ProfileSalutationMissingError;
use Laser\Core\Checkout\Cart\Address\Error\ShippingAddressBlockedError;
use Laser\Core\Checkout\Cart\Address\Error\ShippingAddressSalutationMissingError;
use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Checkout\Cart\CartValidatorInterface;
use Laser\Core\Checkout\Cart\Error\ErrorCollection;
use Laser\Core\Content\Product\State;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Contracts\Service\ResetInterface;

#[Package('checkout')]
class AddressValidator implements CartValidatorInterface, ResetInterface
{
    /**
     * @var array<string, bool>
     */
    private array $available = [];

    /**
     * @internal
     */
    public function __construct(private readonly EntityRepository $repository)
    {
    }

    public function validate(Cart $cart, ErrorCollection $errors, SalesChannelContext $context): void
    {
        $country = $context->getShippingLocation()->getCountry();
        $customer = $context->getCustomer();
        $validateShipping = $cart->getLineItems()->count() === 0
            || $cart->getLineItems()->hasLineItemWithState(State::IS_PHYSICAL);

        if (!$country->getActive() && $validateShipping) {
            $errors->add(new ShippingAddressBlockedError((string) $country->getTranslation('name')));

            return;
        }

        if (!$country->getShippingAvailable() && $validateShipping) {
            $errors->add(new ShippingAddressBlockedError((string) $country->getTranslation('name')));

            return;
        }

        if (!$this->isSalesChannelCountry($country->getId(), $context) && $validateShipping) {
            $errors->add(new ShippingAddressBlockedError((string) $country->getTranslation('name')));

            return;
        }

        if ($customer === null) {
            return;
        }

        if (!$customer->getSalutationId()) {
            $errors->add(new ProfileSalutationMissingError($customer));

            return;
        }

        if ($customer->getActiveBillingAddress() === null || $customer->getActiveShippingAddress() === null) {
            // No need to add salutation-specific errors in this case
            return;
        }

        if (!$customer->getActiveBillingAddress()->getSalutationId()) {
            $errors->add(new BillingAddressSalutationMissingError($customer->getActiveBillingAddress()));

            return;
        }

        if (!$customer->getActiveShippingAddress()->getSalutationId() && $validateShipping) {
            $errors->add(new ShippingAddressSalutationMissingError($customer->getActiveShippingAddress()));
        }
    }

    public function reset(): void
    {
        $this->available = [];
    }

    private function isSalesChannelCountry(string $countryId, SalesChannelContext $context): bool
    {
        if (isset($this->available[$countryId])) {
            return $this->available[$countryId];
        }

        $criteria = new Criteria([$countryId]);
        $criteria->addFilter(new EqualsFilter('salesChannels.id', $context->getSalesChannelId()));

        $salesChannelCountryIds = $this->repository->searchIds($criteria, $context->getContext());

        return $this->available[$countryId] = $salesChannelCountryIds->has($countryId);
    }
}
