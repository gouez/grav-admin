<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Customer\SalesChannel;

use Laser\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\StoreApiResponse;

#[Package('customer-order')]
class AccountNewsletterRecipientRouteResponse extends StoreApiResponse
{
    /**
     * @var AccountNewsletterRecipientResult
     */
    protected $object;

    public function __construct(EntitySearchResult $newsletterRecipients)
    {
        if ($newsletterRecipients->first()) {
            $accNlRecipientResult = new AccountNewsletterRecipientResult($newsletterRecipients->first()->getStatus());
            parent::__construct($accNlRecipientResult);

            return;
        }
        $accNlRecipientResult = new AccountNewsletterRecipientResult();
        parent::__construct($accNlRecipientResult);
    }

    public function getAccountNewsletterRecipient(): AccountNewsletterRecipientResult
    {
        return $this->object;
    }
}
