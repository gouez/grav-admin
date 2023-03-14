<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\Exception;

use Laser\Core\Checkout\Cart\CartException;
use Laser\Core\Framework\Log\Package;

#[Package('checkout')]
class CartTokenNotFoundException extends CartException
{
}
