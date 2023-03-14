<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Customer\Event;

use Laser\Core\Checkout\Customer\Aggregate\CustomerRecovery\CustomerRecoveryDefinition;
use Laser\Core\Checkout\Customer\Aggregate\CustomerRecovery\CustomerRecoveryEntity;
use Laser\Core\Checkout\Customer\CustomerDefinition;
use Laser\Core\Checkout\Customer\CustomerEntity;
use Laser\Core\Content\Flow\Dispatching\Aware\CustomerRecoveryAware;
use Laser\Core\Content\Flow\Dispatching\Aware\ResetUrlAware;
use Laser\Core\Content\Flow\Dispatching\Aware\ShopNameAware;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Event\CustomerAware;
use Laser\Core\Framework\Event\EventData\EntityType;
use Laser\Core\Framework\Event\EventData\EventDataCollection;
use Laser\Core\Framework\Event\EventData\MailRecipientStruct;
use Laser\Core\Framework\Event\EventData\ScalarValueType;
use Laser\Core\Framework\Event\MailAware;
use Laser\Core\Framework\Event\SalesChannelAware;
use Laser\Core\Framework\Event\LaserSalesChannelEvent;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('customer-order')]
class CustomerAccountRecoverRequestEvent extends Event implements SalesChannelAware, LaserSalesChannelEvent, CustomerAware, MailAware, CustomerRecoveryAware, ResetUrlAware, ShopNameAware
{
    public const EVENT_NAME = 'customer.recovery.request';

    /**
     * @var CustomerRecoveryEntity
     */
    private $customerRecovery;

    /**
     * @var SalesChannelContext
     */
    private $salesChannelContext;

    /**
     * @var string
     */
    private $resetUrl;

    /**
     * @var string
     */
    private $shopName;

    /**
     * @var MailRecipientStruct
     */
    private $mailRecipientStruct;

    public function __construct(
        SalesChannelContext $salesChannelContext,
        CustomerRecoveryEntity $customerRecovery,
        string $resetUrl
    ) {
        $this->salesChannelContext = $salesChannelContext;
        $this->customerRecovery = $customerRecovery;
        $this->resetUrl = $resetUrl;
        $this->shopName = $salesChannelContext->getSalesChannel()->getTranslation('name');
    }

    public function getName(): string
    {
        return self::EVENT_NAME;
    }

    public function getCustomerRecovery(): CustomerRecoveryEntity
    {
        return $this->customerRecovery;
    }

    public function getSalesChannelContext(): SalesChannelContext
    {
        return $this->salesChannelContext;
    }

    public function getContext(): Context
    {
        return $this->salesChannelContext->getContext();
    }

    public static function getAvailableData(): EventDataCollection
    {
        return (new EventDataCollection())
            ->add('customerRecovery', new EntityType(CustomerRecoveryDefinition::class))
            ->add('customer', new EntityType(CustomerDefinition::class))
            ->add('resetUrl', new ScalarValueType(ScalarValueType::TYPE_STRING))
            ->add('shopName', new ScalarValueType(ScalarValueType::TYPE_STRING));
    }

    public function getMailStruct(): MailRecipientStruct
    {
        if (!$this->mailRecipientStruct instanceof MailRecipientStruct) {
            /** @var CustomerEntity $customer */
            $customer = $this->customerRecovery->getCustomer();

            $this->mailRecipientStruct = new MailRecipientStruct([
                $customer->getEmail() => $customer->getFirstName() . ' ' . $customer->getLastName(),
            ]);
        }

        return $this->mailRecipientStruct;
    }

    public function getSalesChannelId(): string
    {
        return $this->salesChannelContext->getSalesChannel()->getId();
    }

    public function getResetUrl(): string
    {
        return $this->resetUrl;
    }

    public function getShopName(): string
    {
        return $this->shopName;
    }

    public function getCustomer(): ?CustomerEntity
    {
        return $this->customerRecovery->getCustomer();
    }

    public function getCustomerId(): string
    {
        return $this->getCustomerRecovery()->getCustomerId();
    }

    public function getCustomerRecoveryId(): string
    {
        return $this->customerRecovery->getId();
    }
}
