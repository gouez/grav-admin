<?php declare(strict_types=1);

namespace Laser\Core\Framework\Webhook\Hookable;

use Laser\Core\Checkout\Customer\Aggregate\CustomerAddress\CustomerAddressDefinition;
use Laser\Core\Checkout\Customer\CustomerDefinition;
use Laser\Core\Checkout\Document\DocumentDefinition;
use Laser\Core\Checkout\Order\Aggregate\OrderAddress\OrderAddressDefinition;
use Laser\Core\Checkout\Order\OrderDefinition;
use Laser\Core\Content\Category\CategoryDefinition;
use Laser\Core\Content\Media\MediaDefinition;
use Laser\Core\Content\Product\Aggregate\ProductPrice\ProductPriceDefinition;
use Laser\Core\Content\Product\ProductDefinition;
use Laser\Core\Framework\Api\Acl\Role\AclRoleDefinition;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Laser\Core\Framework\Event\BusinessEventCollector;
use Laser\Core\Framework\Event\BusinessEventDefinition;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Webhook\Hookable;
use Laser\Core\System\SalesChannel\Aggregate\SalesChannelDomain\SalesChannelDomainDefinition;
use Laser\Core\System\SalesChannel\SalesChannelDefinition;

/**
 * @internal only for use by the app-system
 */
#[Package('core')]
class HookableEventCollector
{
    final public const HOOKABLE_ENTITIES = [
        ProductDefinition::ENTITY_NAME,
        ProductPriceDefinition::ENTITY_NAME,
        CategoryDefinition::ENTITY_NAME,
        SalesChannelDefinition::ENTITY_NAME,
        SalesChannelDomainDefinition::ENTITY_NAME,
        CustomerDefinition::ENTITY_NAME,
        CustomerAddressDefinition::ENTITY_NAME,
        OrderDefinition::ENTITY_NAME,
        OrderAddressDefinition::ENTITY_NAME,
        DocumentDefinition::ENTITY_NAME,
        MediaDefinition::ENTITY_NAME,
    ];

    private const PRIVILEGES = 'privileges';

    /**
     * @var string[][][]
     */
    private array $hookableEventNamesWithPrivileges = [];

    public function __construct(
        private readonly BusinessEventCollector $businessEventCollector,
        private readonly DefinitionInstanceRegistry $definitionRegistry
    ) {
    }

    public function getHookableEventNamesWithPrivileges(Context $context): array
    {
        if (!$this->hookableEventNamesWithPrivileges) {
            $this->hookableEventNamesWithPrivileges = $this->getEventNamesWithPrivileges($context);
        }

        return $this->hookableEventNamesWithPrivileges;
    }

    public function getPrivilegesFromBusinessEventDefinition(BusinessEventDefinition $businessEventDefinition): array
    {
        $privileges = [];
        foreach ($businessEventDefinition->getData() as $data) {
            if ($data['type'] !== 'entity') {
                continue;
            }

            $entityName = $this->definitionRegistry->get($data['entityClass'])->getEntityName();
            $privileges[] = $entityName . ':' . AclRoleDefinition::PRIVILEGE_READ;
        }

        return $privileges;
    }

    public function getEntityWrittenEventNamesWithPrivileges(): array
    {
        $entityWrittenEventNames = [];
        foreach (self::HOOKABLE_ENTITIES as $entity) {
            $privileges = [
                self::PRIVILEGES => [$entity . ':' . AclRoleDefinition::PRIVILEGE_READ],
            ];

            $entityWrittenEventNames[$entity . '.written'] = $privileges;
            $entityWrittenEventNames[$entity . '.deleted'] = $privileges;
        }

        return $entityWrittenEventNames;
    }

    private function getEventNamesWithPrivileges(Context $context): array
    {
        return array_merge(
            $this->getEntityWrittenEventNamesWithPrivileges(),
            $this->getBusinessEventNamesWithPrivileges($context),
            $this->getHookableEventNames()
        );
    }

    private function getHookableEventNames(): array
    {
        return array_reduce(array_values(
            array_map(static fn ($hookableEvent) => [$hookableEvent => [self::PRIVILEGES => []]], Hookable::HOOKABLE_EVENTS)
        ), 'array_merge', []);
    }

    private function getBusinessEventNamesWithPrivileges(Context $context): array
    {
        $response = $this->businessEventCollector->collect($context);

        return array_map(function (BusinessEventDefinition $businessEventDefinition) {
            $privileges = $this->getPrivilegesFromBusinessEventDefinition($businessEventDefinition);

            return [
                self::PRIVILEGES => $privileges,
            ];
        }, $response->getElements());
    }
}
