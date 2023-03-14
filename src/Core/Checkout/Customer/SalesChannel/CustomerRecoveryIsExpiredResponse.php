<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Customer\SalesChannel;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\ArrayStruct;
use Laser\Core\System\SalesChannel\StoreApiResponse;

#[Package('customer-order')]
class CustomerRecoveryIsExpiredResponse extends StoreApiResponse
{
    /**
     * @var ArrayStruct<string, bool>
     */
    protected $object;

    public function __construct(bool $expired)
    {
        parent::__construct(new ArrayStruct(['isExpired' => $expired]));
    }

    public function isExpired(): bool
    {
        return $this->object->get('isExpired');
    }
}
