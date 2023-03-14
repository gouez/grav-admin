<?php declare(strict_types=1);

namespace Laser\Core\Framework\Webhook;

use Laser\Core\Framework\Log\Package;

#[Package('core')]
class AclPrivilegeCollection
{
    /**
     * @param array<string> $privileges
     */
    public function __construct(private readonly array $privileges)
    {
    }

    public function isAllowed(string $resource, string $privilege): bool
    {
        return \in_array($resource . ':' . $privilege, $this->privileges, true);
    }
}
