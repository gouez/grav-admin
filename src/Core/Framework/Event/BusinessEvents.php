<?php declare(strict_types=1);

namespace Laser\Core\Framework\Event;

use Laser\Core\Checkout\Cart\Event\CheckoutOrderPlacedEvent;
use Laser\Core\Checkout\Customer\Event\CustomerAccountRecoverRequestEvent;
use Laser\Core\Checkout\Customer\Event\CustomerBeforeLoginEvent;
use Laser\Core\Checkout\Customer\Event\CustomerChangedPaymentMethodEvent;
use Laser\Core\Checkout\Customer\Event\CustomerDeletedEvent;
use Laser\Core\Checkout\Customer\Event\CustomerDoubleOptInRegistrationEvent;
use Laser\Core\Checkout\Customer\Event\CustomerGroupRegistrationAccepted;
use Laser\Core\Checkout\Customer\Event\CustomerGroupRegistrationDeclined;
use Laser\Core\Checkout\Customer\Event\CustomerLoginEvent;
use Laser\Core\Checkout\Customer\Event\CustomerLogoutEvent;
use Laser\Core\Checkout\Customer\Event\CustomerRegisterEvent;
use Laser\Core\Checkout\Customer\Event\DoubleOptInGuestOrderEvent;
use Laser\Core\Checkout\Customer\Event\GuestCustomerRegisterEvent;
use Laser\Core\Checkout\Order\Event\OrderPaymentMethodChangedEvent;
use Laser\Core\Content\ContactForm\Event\ContactFormEvent;
use Laser\Core\Content\MailTemplate\Service\Event\MailBeforeSentEvent;
use Laser\Core\Content\MailTemplate\Service\Event\MailBeforeValidateEvent;
use Laser\Core\Content\MailTemplate\Service\Event\MailSentEvent;
use Laser\Core\Content\Newsletter\Event\NewsletterConfirmEvent;
use Laser\Core\Content\Newsletter\Event\NewsletterRegisterEvent;
use Laser\Core\Content\Newsletter\Event\NewsletterUnsubscribeEvent;
use Laser\Core\Content\Product\SalesChannel\Review\Event\ReviewFormEvent;
use Laser\Core\Content\ProductExport\Event\ProductExportLoggingEvent;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\User\Recovery\UserRecoveryRequestEvent;

#[Package('business-ops')]
final class BusinessEvents
{
    /**
     * @Event("Laser\Core\Checkout\Customer\Event\CustomerBeforeLoginEvent")
     */
    public const CHECKOUT_CUSTOMER_BEFORE_LOGIN = CustomerBeforeLoginEvent::EVENT_NAME;

    /**
     * @Event("Laser\Core\Checkout\Customer\Event\CustomerLoginEvent")
     */
    public const CHECKOUT_CUSTOMER_LOGIN = CustomerLoginEvent::EVENT_NAME;

    /**
     * @Event("Laser\Core\Checkout\Customer\Event\CustomerLogoutEvent")
     */
    public const CHECKOUT_CUSTOMER_LOGOUT = CustomerLogoutEvent::EVENT_NAME;

    /**
     * @Event("Laser\Core\Checkout\Customer\Event\CustomerDeletedEvent")
     */
    public const CHECKOUT_CUSTOMER_DELETED = CustomerDeletedEvent::EVENT_NAME;

    /**
     * @Event("Laser\Core\System\User\Recovery\UserRecoveryRequestEvent")
     */
    public const USER_RECOVERY_REQUEST = UserRecoveryRequestEvent::EVENT_NAME;

    /**
     * @Event("Laser\Core\Checkout\Customer\Event\CustomerChangedPaymentMethodEvent")
     */
    public const CHECKOUT_CUSTOMER_CHANGED_PAYMENT_METHOD = CustomerChangedPaymentMethodEvent::EVENT_NAME;

    /**
     * @Event("Laser\Core\Checkout\Cart\Event\CheckoutOrderPlacedEvent")
     */
    public const CHECKOUT_ORDER_PLACED = CheckoutOrderPlacedEvent::EVENT_NAME;

    /**
     * @Event("Laser\Core\Checkout\Order\Event\OrderPaymentMethodChangedEvent")
     */
    public const CHECKOUT_ORDER_PAYMENT_METHOD_CHANGED = OrderPaymentMethodChangedEvent::EVENT_NAME;

    /**
     * @Event("Laser\Core\Checkout\Customer\Event\CustomerAccountRecoverRequestEvent")
     */
    public const CUSTOMER_ACCOUNT_RECOVER_REQUEST = CustomerAccountRecoverRequestEvent::EVENT_NAME;

    /**
     * @Event("Laser\Core\Checkout\Customer\Event\CustomerDoubleOptInRegistrationEvent")
     */
    public const CUSTOMER_DOUBLE_OPT_IN_REGISTRATION = CustomerDoubleOptInRegistrationEvent::EVENT_NAME;

    /**
     * @Event("Laser\Core\Checkout\Customer\Event\CustomerGroupRegistrationAccepted")
     */
    public const CUSTOMER_GROUP_REGISTRATION_ACCEPTED = CustomerGroupRegistrationAccepted::EVENT_NAME;

    /**
     * @Event("Laser\Core\Checkout\Customer\Event\CustomerGroupRegistrationDeclined")
     */
    public const CUSTOMER_GROUP_REGISTRATION_DECLINED = CustomerGroupRegistrationDeclined::EVENT_NAME;

    /**
     * @Event("Laser\Core\Checkout\Customer\Event\CustomerRegisterEvent")
     */
    public const CUSTOMER_REGISTER = CustomerRegisterEvent::EVENT_NAME;

    /**
     * @Event("Laser\Core\Checkout\Customer\Event\DoubleOptInGuestOrderEvent")
     */
    public const DOUBLE_OPT_IN_GUEST_ORDER = DoubleOptInGuestOrderEvent::EVENT_NAME;

    /**
     * @Event("Laser\Core\Checkout\Customer\Event\GuestCustomerRegisterEvent")
     */
    public const GUEST_CUSTOMER_REGISTER = GuestCustomerRegisterEvent::EVENT_NAME;

    /**
     * @Event("Laser\Core\Content\ContactForm\Event\ContactFormEvent")
     */
    public const CONTACT_FORM = ContactFormEvent::EVENT_NAME;

    /**
     * @Event("Laser\Core\Content\Product\SalesChannel\Review\Event\ReviewFormEvent")
     */
    public const REVIEW_FORM = ReviewFormEvent::EVENT_NAME;

    /**
     * @Event("Laser\Core\Content\MailTemplate\Service\Event\MailBeforeSentEvent")
     */
    public const MAIL_BEFORE_SENT = MailBeforeSentEvent::EVENT_NAME;

    /**
     * @Event("Laser\Core\Content\MailTemplate\Service\Event\MailBeforeValidateEvent")
     */
    public const MAIL_BEFORE_VALIDATE = MailBeforeValidateEvent::EVENT_NAME;

    /**
     * @Event("Laser\Core\Content\MailTemplate\Service\Event\MailSentEvent")
     */
    public const MAIL_SENT = MailSentEvent::EVENT_NAME;

    /**
     * @Event("Laser\Core\Content\Newsletter\Event\NewsletterConfirmEvent")
     */
    public const NEWSLETTER_CONFIRM = NewsletterConfirmEvent::EVENT_NAME;

    /**
     * @Event("Laser\Core\Content\Newsletter\Event\NewsletterRegisterEvent")
     */
    public const NEWSLETTER_REGISTER = NewsletterRegisterEvent::EVENT_NAME;

    /**
     * @Event("Laser\Core\Content\Newsletter\Event\NewsletterUnsubscribeEvent")
     */
    public const NEWSLETTER_UNSUBSCRIBE = NewsletterUnsubscribeEvent::EVENT_NAME;

    /**
     * @Event("Laser\Core\Content\ProductExport\Event\ProductExportLoggingEvent")
     */
    public const PRODUCT_EXPORT_LOGGING = ProductExportLoggingEvent::NAME;

    private function __construct()
    {
    }
}
