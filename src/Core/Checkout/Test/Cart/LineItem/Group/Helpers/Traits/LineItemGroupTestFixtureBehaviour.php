<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Cart\LineItem\Group\Helpers\Traits;

use Laser\Core\Checkout\Cart\LineItem\Group\LineItemGroupDefinition;
use Laser\Core\Content\Rule\RuleCollection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Uuid\Uuid;

#[Package('checkout')]
trait LineItemGroupTestFixtureBehaviour
{
    private function buildGroup(string $packagerKey, float $value, string $sorterKey, RuleCollection $rules): LineItemGroupDefinition
    {
        $group = new LineItemGroupDefinition(
            Uuid::randomBytes(),
            $packagerKey,
            $value,
            $sorterKey,
            $rules
        );

        return $group;
    }
}
