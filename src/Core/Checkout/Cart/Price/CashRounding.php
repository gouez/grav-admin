<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\Price;

use Laser\Core\Framework\DataAbstractionLayer\Pricing\CashRoundingConfig;
use Laser\Core\Framework\Log\Package;

#[Package('checkout')]
class CashRounding
{
    public function mathRound(float $price, CashRoundingConfig $config): float
    {
        return round($price, $config->getDecimals());
    }

    public function cashRound(float $price, CashRoundingConfig $config): float
    {
        $rounded = $this->mathRound($price, $config);

        if ($config->getDecimals() > 2) {
            return $rounded;
        }

        $multiplier = 100 / ($config->getInterval() * 100);

        return round($rounded * $multiplier, 0) / $multiplier;
    }
}
