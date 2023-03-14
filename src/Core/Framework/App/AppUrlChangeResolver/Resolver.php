<?php declare(strict_types=1);

namespace Laser\Core\Framework\App\AppUrlChangeResolver;

use Laser\Core\Framework\App\Exception\AppUrlChangeStrategyNotFoundException;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Log\Package;

/**
 * @internal only for use by the app-system, will be considered internal from v6.4.0 onward
 */
#[Package('core')]
class Resolver
{
    /**
     * @param AbstractAppUrlChangeStrategy[] $strategies
     */
    public function __construct(private readonly iterable $strategies)
    {
    }

    public function resolve(string $strategyName, Context $context): void
    {
        /** @var AbstractAppUrlChangeStrategy $strategy */
        foreach ($this->strategies as $strategy) {
            if ($strategy->getName() === $strategyName) {
                $strategy->resolve($context);

                return;
            }
        }

        throw new AppUrlChangeStrategyNotFoundException($strategyName);
    }

    /**
     * @return array<string>
     */
    public function getAvailableStrategies(): array
    {
        $strategies = [];

        /** @var AbstractAppUrlChangeStrategy $strategy */
        foreach ($this->strategies as $strategy) {
            $strategies[$strategy->getName()] = $strategy->getDescription();
        }

        return $strategies;
    }
}
