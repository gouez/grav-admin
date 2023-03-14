<?php declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer\Field\Flag;

use Laser\Core\Framework\Log\Package;

#[Package('core')]
class PrimaryKey extends Flag
{
    public function parse(): \Generator
    {
        yield 'primary_key' => true;
    }
}
