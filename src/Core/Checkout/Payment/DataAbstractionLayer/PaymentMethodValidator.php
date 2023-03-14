<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Payment\DataAbstractionLayer;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Laser\Core\Checkout\Payment\Exception\PluginPaymentMethodsDeleteRestrictionException;
use Laser\Core\Checkout\Payment\PaymentMethodDefinition;
use Laser\Core\Framework\DataAbstractionLayer\Write\Validation\PreWriteValidationEvent;
use Laser\Core\Framework\Log\Package;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @internal
 */
#[Package('core')]
final class PaymentMethodValidator implements EventSubscriberInterface
{
    /**
     * @internal
     */
    public function __construct(private readonly Connection $connection)
    {
    }

    /**
     * @return array<string, string|array{0: string, 1: int}|list<array{0: string, 1?: int}>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            PreWriteValidationEvent::class => 'validate',
        ];
    }

    public function validate(PreWriteValidationEvent $event): void
    {
        $ids = $event->getDeletedPrimaryKeys(PaymentMethodDefinition::ENTITY_NAME);

        $ids = \array_column($ids, 'id');

        if (empty($ids)) {
            return;
        }

        $pluginIds = $this->connection->fetchOne(
            'SELECT id FROM payment_method WHERE id IN (:ids) AND plugin_id IS NOT NULL',
            ['ids' => $ids],
            ['ids' => ArrayParameterType::STRING]
        );

        if (!empty($pluginIds)) {
            throw new PluginPaymentMethodsDeleteRestrictionException();
        }
    }
}
