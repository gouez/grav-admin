<?php declare(strict_types=1);

namespace Laser\Core\Content\Flow\Dispatching\Action;

use Doctrine\DBAL\Connection;
use Laser\Core\Checkout\Customer\Aggregate\CustomerGroup\CustomerGroupEntity;
use Laser\Core\Content\Flow\Dispatching\DelayableAction;
use Laser\Core\Content\Flow\Dispatching\StorableFlow;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\Event\CustomerGroupAware;
use Laser\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('business-ops')]
class SetCustomerGroupCustomFieldAction extends FlowAction implements DelayableAction
{
    use CustomFieldActionTrait;

    private readonly Connection $connection;

    /**
     * @internal
     */
    public function __construct(
        Connection $connection,
        private readonly EntityRepository $customerGroupRepository
    ) {
        $this->connection = $connection;
    }

    public static function getName(): string
    {
        return 'action.set.customer.group.custom.field';
    }

    /**
     * @return array<int, string>
     */
    public function requirements(): array
    {
        return [CustomerGroupAware::class];
    }

    public function handleFlow(StorableFlow $flow): void
    {
        if (!$flow->hasStore(CustomerGroupAware::CUSTOMER_GROUP_ID)) {
            return;
        }

        $this->update($flow->getContext(), $flow->getConfig(), $flow->getStore(CustomerGroupAware::CUSTOMER_GROUP_ID));
    }

    /**
     * @param array<string, mixed> $config
     */
    private function update(Context $context, array $config, string $customerGroupId): void
    {
        /** @var CustomerGroupEntity $customerGroup */
        $customerGroup = $this->customerGroupRepository->search(new Criteria([$customerGroupId]), $context)->first();

        $customFields = $this->getCustomFieldForUpdating($customerGroup->getCustomfields(), $config);

        if ($customFields === null) {
            return;
        }

        $customFields = empty($customFields) ? null : $customFields;

        $this->customerGroupRepository->update([
            [
                'id' => $customerGroupId,
                'customFields' => $customFields,
            ],
        ], $context);
    }
}
