<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Customer\DataAbstractionLayer;

use Laser\Core\Checkout\Customer\Exception\DuplicateWishlistProductException;
use Laser\Core\Framework\DataAbstractionLayer\Dbal\ExceptionHandlerInterface;
use Laser\Core\Framework\Log\Package;

#[Package('core')]
class CustomerWishlistProductExceptionHandler implements ExceptionHandlerInterface
{
    public function getPriority(): int
    {
        return ExceptionHandlerInterface::PRIORITY_DEFAULT;
    }

    public function matchException(\Exception $e): ?\Exception
    {
        if (preg_match('/SQLSTATE\[23000\]:.*1062 Duplicate.*uniq.customer_wishlist.sales_channel_id__customer_id\'/', $e->getMessage())) {
            return new DuplicateWishlistProductException();
        }

        return null;
    }
}
