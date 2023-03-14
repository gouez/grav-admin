<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\Webhook\_fixtures\BusinessEvents;

use Laser\Core\Framework\Context;
use Laser\Core\Framework\Event\EventData\EntityType;
use Laser\Core\Framework\Event\EventData\EventDataCollection;
use Laser\Core\Framework\Event\EventData\ObjectType;
use Laser\Core\Framework\Event\FlowEventAware;
use Laser\Core\System\Tax\TaxDefinition;
use Laser\Core\System\Tax\TaxEntity;

/**
 * @internal
 */
class NestedEntityBusinessEvent implements FlowEventAware, BusinessEventEncoderTestInterface
{
    public function __construct(private readonly TaxEntity $tax)
    {
    }

    public static function getAvailableData(): EventDataCollection
    {
        return (new EventDataCollection())
            ->add('object', (new ObjectType())
                ->add('tax', new EntityType(TaxDefinition::class)));
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getEncodeValues(string $laserVersion): array
    {
        return [
            'object' => [
                'tax' => [
                    'id' => $this->tax->getId(),
                    '_uniqueIdentifier' => $this->tax->getId(),
                    'versionId' => null,
                    'name' => $this->tax->getName(),
                    'taxRate' => $this->tax->getTaxRate(),
                    'position' => $this->tax->getPosition(),
                    'customFields' => null,
                    'translated' => [],
                    'createdAt' => $this->tax->getCreatedAt() ? $this->tax->getCreatedAt()->format(\DATE_RFC3339_EXTENDED) : null,
                    'updatedAt' => null,
                    'extensions' => [],
                    'apiAlias' => 'tax',
                ],
            ],
        ];
    }

    public function getName(): string
    {
        return 'test';
    }

    public function getContext(): Context
    {
        return Context::createDefaultContext();
    }

    public function getObject(): EntityBusinessEvent
    {
        return new EntityBusinessEvent($this->tax);
    }
}
