<?php declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer;

use Laser\Core\Framework\Log\Package;

/**
 * @internal
 * Contains all registered entity extensions in the system
 */
#[Package('core')]
class ExtensionRegistry
{
    /**
     * @internal
     *
     * @param iterable<EntityExtension> $extensions
     */
    public function __construct(private readonly iterable $extensions)
    {
    }

    /**
     * @return iterable<EntityExtension>
     */
    public function getExtensions(): iterable
    {
        return $this->extensions;
    }
}
