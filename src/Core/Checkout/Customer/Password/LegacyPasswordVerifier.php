<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Customer\Password;

use Laser\Core\Checkout\Customer\CustomerEntity;
use Laser\Core\Checkout\Customer\Exception\LegacyPasswordEncoderNotFoundException;
use Laser\Core\Checkout\Customer\Password\LegacyEncoder\LegacyEncoderInterface;
use Laser\Core\Framework\Log\Package;

#[Package('customer-order')]
class LegacyPasswordVerifier
{
    /**
     * @internal
     *
     * @param LegacyEncoderInterface[] $encoder
     */
    public function __construct(private readonly iterable $encoder)
    {
    }

    public function verify(string $password, CustomerEntity $customer): bool
    {
        foreach ($this->encoder as $encoder) {
            if ($encoder->getName() !== $customer->getLegacyEncoder()) {
                continue;
            }

            return $encoder->isPasswordValid($password, $customer->getLegacyPassword());
        }

        throw new LegacyPasswordEncoderNotFoundException($customer->getLegacyEncoder());
    }
}
