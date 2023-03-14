<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Promotion\Service;

use Laser\Core\Framework\Log\Package;

#[Package('checkout')]
interface PromotionDateTimeServiceInterface
{
    public function getNow(): string;
}
