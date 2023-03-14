<?php declare(strict_types=1);

namespace Laser\Core\Content\Media\Message;

use League\Flysystem\Visibility;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\MessageQueue\AsyncMessageInterface;

#[Package('content')]
class DeleteFileMessage implements AsyncMessageInterface
{
    public function __construct(
        private array $files = [],
        private string $visibility = Visibility::PUBLIC
    ) {
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function setFiles(array $files): void
    {
        $this->files = $files;
    }

    public function getVisibility(): string
    {
        return $this->visibility;
    }

    public function setVisibility(string $visibility): void
    {
        $this->visibility = $visibility;
    }
}
