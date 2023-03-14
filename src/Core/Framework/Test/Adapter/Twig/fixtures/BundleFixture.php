<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\Adapter\Twig\fixtures;

use Laser\Core\Framework\Bundle;

/**
 * @internal
 */
class BundleFixture extends Bundle
{
    public function __construct(
        string $name,
        string $path
    ) {
        $this->name = $name;
        $this->path = $path;
    }
}
