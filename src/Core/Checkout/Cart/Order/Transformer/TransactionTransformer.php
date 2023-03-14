<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\Order\Transformer;

use Laser\Core\Checkout\Cart\Transaction\Struct\Transaction;
use Laser\Core\Checkout\Cart\Transaction\Struct\TransactionCollection;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Log\Package;

#[Package('checkout')]
class TransactionTransformer
{
    public static function transformCollection(
        TransactionCollection $transactions,
        string $stateId,
        Context $context
    ): array {
        $output = [];
        foreach ($transactions as $transaction) {
            $output[] = self::transform($transaction, $stateId, $context);
        }

        return $output;
    }

    public static function transform(
        Transaction $transaction,
        string $stateId,
        Context $context
    ): array {
        return [
            'paymentMethodId' => $transaction->getPaymentMethodId(),
            'amount' => $transaction->getAmount(),
            'stateId' => $stateId,
        ];
    }
}
