<?php declare(strict_types=1);

namespace Laser\Core\System\Snippet\Filter;

use Laser\Core\Framework\Log\Package;

#[Package('system-settings')]
interface SnippetFilterInterface
{
    public function getName(): string;

    public function supports(string $name): bool;

    public function filter(array $snippets, $requestFilterValue): array;
}
