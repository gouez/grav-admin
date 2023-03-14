<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Order;

use Laser\Core\Checkout\Order\Exception\LanguageOfOrderDeleteException;
use Laser\Core\Framework\DataAbstractionLayer\Dbal\ExceptionHandlerInterface;
use Laser\Core\Framework\Log\Package;

#[Package('customer-order')]
class OrderExceptionHandler implements ExceptionHandlerInterface
{
    public function getPriority(): int
    {
        return ExceptionHandlerInterface::PRIORITY_DEFAULT;
    }

    public function matchException(\Exception $e): ?\Exception
    {
        if (preg_match('/SQLSTATE\[23000\]:.*1451.*a foreign key constraint.*order.*CONSTRAINT `fk.language_id`/', $e->getMessage())) {
            return new LanguageOfOrderDeleteException($e);
        }

        return null;
    }
}
