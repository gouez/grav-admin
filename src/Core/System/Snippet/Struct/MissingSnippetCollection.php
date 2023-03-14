<?php declare(strict_types=1);

namespace Laser\Core\System\Snippet\Struct;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\Collection;

/**
 * @extends Collection<MissingSnippetStruct>
 */
#[Package('system-settings')]
class MissingSnippetCollection extends Collection
{
}
