<?php declare(strict_types=1);

namespace Laser\Core\System\Snippet;

use Laser\Core\Framework\Log\Package;

#[Package('system-settings')]
interface SnippetValidatorInterface
{
    public function validate(): array;
}
