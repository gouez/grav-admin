<?php declare(strict_types=1);

namespace Laser\Core\Content\ImportExport\DataAbstractionLayer\Serializer\Entity;

use Laser\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemCollection;
use Laser\Core\Checkout\Order\OrderDefinition;
use Laser\Core\Content\ImportExport\Struct\Config;
use Laser\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\Struct;

#[Package('core')]
class OrderSerializer extends EntitySerializer
{
    public function supports(string $entity): bool
    {
        return $entity === OrderDefinition::ENTITY_NAME;
    }

    public function serialize(Config $config, EntityDefinition $definition, $entity): iterable
    {
        if ($entity === null) {
            return;
        }

        if ($entity instanceof Struct) {
            $entity = $entity->jsonSerialize();
        }

        yield from parent::serialize($config, $definition, $entity);

        if (isset($entity['lineItems'])) {
            /** @var OrderLineItemCollection $lineItems */
            $lineItems = $entity['lineItems']->getElements();
            $modifiedLineItems = [];

            foreach ($lineItems as $lineItem) {
                $lineItem = $lineItem->jsonSerialize();

                $modifiedLineItems[] = $lineItem['quantity'] . 'x ' . $lineItem['productId'];
            }

            $entity['lineItems'] = implode('|', $modifiedLineItems);
        }

        if (isset($entity['deliveries']) && (is_countable($entity['deliveries']) ? \count($entity['deliveries']) : 0) > 0) {
            $entity['deliveries'] = $entity['deliveries']->first()->jsonSerialize();
            if (!empty($entity['deliveries']['trackingCodes'])) {
                $entity['deliveries']['trackingCodes'] = implode('|', $entity['deliveries']['trackingCodes']);
            }

            if (!empty($entity['deliveries']['shippingOrderAddress'])) {
                $entity['deliveries']['shippingOrderAddress'] = $entity['deliveries']['shippingOrderAddress']->jsonSerialize();
            }
        }

        yield from $entity;
    }
}
