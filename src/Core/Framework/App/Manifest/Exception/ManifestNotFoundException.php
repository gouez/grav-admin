<?php declare(strict_types=1);

namespace Laser\Core\Framework\App\Manifest\Exception;

use Laser\Core\Framework\Log\Package;

/**
 * @internal only for use by the app-system
 */
#[Package('core')]
class ManifestNotFoundException extends \RuntimeException
{
    public function __construct(string $path)
    {
        parent::__construct(sprintf(
            'No "manifest.xml" file in path "%s" found. (The file must be placed in the app root folder.)',
            $path
        ));
    }
}
