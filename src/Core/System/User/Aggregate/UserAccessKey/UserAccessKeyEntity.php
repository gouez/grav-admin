<?php declare(strict_types=1);

namespace Laser\Core\System\User\Aggregate\UserAccessKey;

use Laser\Core\Framework\DataAbstractionLayer\Entity;
use Laser\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use Laser\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\User\UserEntity;

#[Package('system-settings')]
class UserAccessKeyEntity extends Entity
{
    use EntityIdTrait;
    use EntityCustomFieldsTrait;

    /**
     * @var string
     */
    protected $userId;

    /**
     * @var string
     */
    protected $accessKey;

    /**
     * @var string
     */
    protected $secretAccessKey;

    /**
     * @var \DateTimeInterface|null
     */
    protected $lastUsageAt;

    /**
     * @var UserEntity|null
     */
    protected $user;

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }

    public function getAccessKey(): string
    {
        return $this->accessKey;
    }

    public function setAccessKey(string $accessKey): void
    {
        $this->accessKey = $accessKey;
    }

    public function getSecretAccessKey(): string
    {
        return $this->secretAccessKey;
    }

    public function setSecretAccessKey(string $secretAccessKey): void
    {
        $this->secretAccessKey = $secretAccessKey;
    }

    public function getLastUsageAt(): ?\DateTimeInterface
    {
        return $this->lastUsageAt;
    }

    public function setLastUsageAt(\DateTimeInterface $lastUsageAt): void
    {
        $this->lastUsageAt = $lastUsageAt;
    }

    public function getUser(): ?UserEntity
    {
        return $this->user;
    }

    public function setUser(UserEntity $user): void
    {
        $this->user = $user;
    }
}
