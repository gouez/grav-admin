<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\Transaction\Struct;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\Collection;

/**
 * @extends Collection<Transaction>
 */
#[Package('checkout')]
class TransactionCollection extends Collection
{
    public function getApiAlias(): string
    {
        return 'cart_transaction_collection';
    }

    protected function getExpectedClass(): ?string
    {
        return Transaction::class;
    }
}
