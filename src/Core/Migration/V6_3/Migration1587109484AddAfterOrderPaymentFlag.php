<?php declare(strict_types=1);

namespace Laser\Core\Migration\V6_3;

use Doctrine\DBAL\Connection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
#[Package('core')]
class Migration1587109484AddAfterOrderPaymentFlag extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1587109484;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement(
            'ALTER TABLE payment_method
            ADD COLUMN `after_order_enabled` TINYINT(1) NOT NULL DEFAULT 0 AFTER `active`'
        );

        $connection->executeStatement(
            'UPDATE `payment_method`
            SET `after_order_enabled` = 1 WHERE `handler_identifier` IN (
                "Laser\\\Core\\\Checkout\\\Payment\\\Cart\\\PaymentHandler\\\DebitPayment",
                "Laser\\\Core\\\Checkout\\\Payment\\\Cart\\\PaymentHandler\\\CashPayment",
                "Laser\\\Core\\\Checkout\\\Payment\\\Cart\\\PaymentHandler\\\PrePayment",
                "Laser\\\Core\\\Checkout\\\Payment\\\Cart\\\PaymentHandler\\\InvoicePayment"
            )'
        );
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
