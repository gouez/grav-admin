<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Cart\Processor\_fixtures;

use Laser\Core\Checkout\Cart\Tax\Struct\TaxRule;
use Laser\Core\Checkout\Cart\Tax\Struct\TaxRuleCollection;
use Laser\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('checkout')]
class LowTaxes extends TaxRuleCollection
{
    public function __construct()
    {
        parent::__construct([new TaxRule(7)]);
    }
}
