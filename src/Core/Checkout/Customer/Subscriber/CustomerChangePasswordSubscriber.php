<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Customer\Subscriber;

use Doctrine\DBAL\Connection;
use Laser\Core\Checkout\Customer\CustomerEvents;
use Laser\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @internal
 */
#[Package('customer-order')]
class CustomerChangePasswordSubscriber implements EventSubscriberInterface
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
            CustomerEvents::CUSTOMER_WRITTEN_EVENT => 'onCustomerWritten',
        ];
    }

    public function onCustomerWritten(EntityWrittenEvent $event): void
    {
        $payloads = $event->getPayloads();
        foreach ($payloads as $payload) {
            if (!empty($payload['password'])) {
                $this->clearLegacyPassword($payload['id']);
            }
        }
    }

    private function clearLegacyPassword(string $customerId): void
    {
        $this->connection->executeStatement(
            'UPDATE `customer` SET `legacy_password` = null, `legacy_encoder` = null WHERE id = :id',
            [
                'id' => Uuid::fromHexToBytes($customerId),
            ]
        );
    }
}
