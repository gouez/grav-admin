<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\SalesChannel;

use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\StoreApiResponse;

#[Package('checkout')]
class CartResponse extends StoreApiResponse
{
    /**
     * @var Cart
     */
    protected $object;

    public function __construct(Cart $object)
    {
        parent::__construct($object);
    }

    public function getCart(): Cart
    {
        return $this->object;
    }
}
