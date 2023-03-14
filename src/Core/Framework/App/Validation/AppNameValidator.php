<?php declare(strict_types=1);

namespace Laser\Core\Framework\App\Validation;

use Laser\Core\Framework\App\Manifest\Manifest;
use Laser\Core\Framework\App\Validation\Error\AppNameError;
use Laser\Core\Framework\App\Validation\Error\ErrorCollection;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Log\Package;

/**
 * @internal only for use by the app-system
 */
#[Package('core')]
class AppNameValidator extends AbstractManifestValidator
{
    public function validate(Manifest $manifest, ?Context $context): ErrorCollection
    {
        $errors = new ErrorCollection();

        $appName = substr($manifest->getPath(), strrpos($manifest->getPath(), '/') + 1);

        if ($appName !== $manifest->getMetadata()->getName()) {
            $errors->add(new AppNameError($manifest->getMetadata()->getName()));
        }

        return $errors;
    }
}
