<?php declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer\Search\Term;

use Laser\Core\Framework\Log\Package;

#[Package('core')]
interface TokenizerInterface
{
    /**
     * @return array<string>
     */
    public function tokenize(string $string): array;
}
