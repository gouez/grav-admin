<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Order\Event;

use Laser\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionDefinition;
use Laser\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionEntity;
use Laser\Core\Checkout\Order\OrderDefinition;
use Laser\Core\Checkout\Order\OrderEntity;
use Laser\Core\Content\Flow\Dispatching\Aware\OrderTransactionAware;
use Laser\Core\Content\Flow\Exception\CustomerDeletedException;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\Exception\AssociationNotFoundException;
use Laser\Core\Framework\Event\CustomerAware;
use Laser\Core\Framework\Event\EventData\EntityType;
use Laser\Core\Framework\Event\EventData\EventDataCollection;
use Laser\Core\Framework\Event\EventData\MailRecipientStruct;
use Laser\Core\Framework\Event\MailAware;
use Laser\Core\Framework\Event\OrderAware;
use Laser\Core\Framework\Event\SalesChannelAware;
use Laser\Core\Framework\Log\Package;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('customer-order')]
class OrderPaymentMethodChangedEvent extends Event implements SalesChannelAware, OrderAware, CustomerAware, MailAware, OrderTransactionAware
{
    final public const EVENT_NAME = 'checkout.order.payment_method.changed';

    public function __construct(
        private readonly OrderEntity $order,
        private readonly OrderTransactionEntity $orderTransaction,
        private readonly Context $context,
        private readonly string $salesChannelId,
        private ?MailRecipientStruct $mailRecipientStruct = null
    ) {
    }

    public function getName(): string
    {
        return self::EVENT_NAME;
    }

    public function getOrder(): OrderEntity
    {
        return $this->order;
    }

    public function getOrderTransaction(): OrderTransactionEntity
    {
        return $this->orderTransaction;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function getMailStruct(): MailRecipientStruct
    {
        if (!$this->mailRecipientStruct instanceof MailRecipientStruct) {
            $orderCustomer = $this->order->getOrderCustomer();
            if ($orderCustomer === null) {
                throw new AssociationNotFoundException('orderCustomer');
            }

            $this->mailRecipientStruct = new MailRecipientStruct([
                $orderCustomer->getEmail() => $orderCustomer->getFirstName() . ' ' . $orderCustomer->getLastName(),
            ]);
        }

        return $this->mailRecipientStruct;
    }

    public function getSalesChannelId(): string
    {
        return $this->salesChannelId;
    }

    public function getOrderId(): string
    {
        return $this->order->getId();
    }

    public static function getAvailableData(): EventDataCollection
    {
        return (new EventDataCollection())
            ->add('order', new EntityType(OrderDefinition::class))
            ->add('orderTransaction', new EntityType(OrderTransactionDefinition::class));
    }

    public function getCustomerId(): string
    {
        $customer = $this->getOrder()->getOrderCustomer();

        if ($customer === null || $customer->getCustomerId() === null) {
            throw new CustomerDeletedException($this->getOrderId());
        }

        return $customer->getCustomerId();
    }

    public function getOrderTransactionId(): string
    {
        return $this->orderTransaction->getId();
    }
}
