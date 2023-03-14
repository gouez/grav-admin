<?php declare(strict_types=1);

namespace Laser\Core\Framework\Api\Acl\Role;

use Laser\Core\Framework\App\AppEntity;
use Laser\Core\Framework\DataAbstractionLayer\Entity;
use Laser\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\Integration\IntegrationCollection;
use Laser\Core\System\User\UserCollection;

#[Package('core')]
class AclRoleEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $description;

    /**
     * @var array
     */
    protected $privileges = [];

    /**
     * @var UserCollection|null
     */
    protected $users;

    /**
     * @var AppEntity|null
     */
    protected $app;

    /**
     * @var IntegrationCollection|null
     */
    protected $integrations;

    protected ?\DateTimeInterface $deletedAt = null;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getUsers(): ?UserCollection
    {
        return $this->users;
    }

    public function setUsers(UserCollection $users): void
    {
        $this->users = $users;
    }

    public function getPrivileges(): array
    {
        return $this->privileges;
    }

    public function setPrivileges(array $privileges): void
    {
        $this->privileges = $privileges;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getApp(): ?AppEntity
    {
        return $this->app;
    }

    public function setApp(?AppEntity $app): void
    {
        $this->app = $app;
    }

    public function getIntegrations(): ?IntegrationCollection
    {
        return $this->integrations;
    }

    public function setIntegrations(IntegrationCollection $integrations): void
    {
        $this->integrations = $integrations;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(\DateTimeInterface $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }
}
