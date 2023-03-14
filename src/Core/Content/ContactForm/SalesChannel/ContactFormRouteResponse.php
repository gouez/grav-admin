<?php declare(strict_types=1);

namespace Laser\Core\Content\ContactForm\SalesChannel;

use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\StoreApiResponse;

#[Package('content')]
class ContactFormRouteResponse extends StoreApiResponse
{
    /**
     * @var ContactFormRouteResponseStruct
     */
    protected $object;

    public function __construct(ContactFormRouteResponseStruct $object)
    {
        parent::__construct($object);
    }

    public function getResult(): ContactFormRouteResponseStruct
    {
        return $this->object;
    }
}
