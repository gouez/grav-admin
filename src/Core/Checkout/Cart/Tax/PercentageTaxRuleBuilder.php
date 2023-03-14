<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\Tax;

use Laser\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Laser\Core\Checkout\Cart\Tax\Struct\TaxRule;
use Laser\Core\Checkout\Cart\Tax\Struct\TaxRuleCollection;
use Laser\Core\Framework\Log\Package;

#[Package('checkout')]
class PercentageTaxRuleBuilder
{
    public function buildRules(CalculatedPrice $price): TaxRuleCollection
    {
        $rules = new TaxRuleCollection([]);

        foreach ($price->getCalculatedTaxes() as $tax) {
            $percentage = 0;
            if ($price->getTotalPrice() > 0) {
                $percentage = $tax->getPrice() / $price->getTotalPrice() * 100;
            }

            $rules->add(
                new TaxRule(
                    $tax->getTaxRate(),
                    $percentage
                )
            );
        }

        return $rules;
    }
}
