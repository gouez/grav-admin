<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Customer\Password\LegacyEncoder;

use Laser\Core\Framework\Log\Package;

#[Package('customer-order')]
interface LegacyEncoderInterface
{
    public function getName(): string;

    public function isPasswordValid(string $password, string $hash): bool;
}
