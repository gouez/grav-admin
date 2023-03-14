<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Document\DocumentGenerator;

use Laser\Core\Framework\Log\Package;

#[Package('customer-order')]
class Counter
{
    private int $counter = 0;

    public function getCounter(): int
    {
        return $this->counter;
    }

    public function increment(): void
    {
        $this->counter = $this->counter + 1;
    }
}
