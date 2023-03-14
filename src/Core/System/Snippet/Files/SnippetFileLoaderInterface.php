<?php declare(strict_types=1);

namespace Laser\Core\System\Snippet\Files;

use Laser\Core\Framework\Log\Package;

#[Package('system-settings')]
interface SnippetFileLoaderInterface
{
    public function loadSnippetFilesIntoCollection(SnippetFileCollection $snippetFileCollection): void;
}
