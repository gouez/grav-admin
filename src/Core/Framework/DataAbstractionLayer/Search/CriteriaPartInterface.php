<?php declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer\Search;

use Laser\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
interface CriteriaPartInterface
{
    /**
     * @return list<string>
     */
    public function getFields(): array;
}
