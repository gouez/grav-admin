<?php declare(strict_types=1);

namespace Laser\Core\Framework\App\Validation;

use Laser\Core\Framework\App\Manifest\Manifest;
use Laser\Core\Framework\App\Validation\Error\ErrorCollection;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Log\Package;

/**
 * @internal only for use by the app-system
 */
#[Package('core')]
class TranslationValidator extends AbstractManifestValidator
{
    public function validate(Manifest $manifest, ?Context $context): ErrorCollection
    {
        $errors = new ErrorCollection();
        $error = $manifest->getMetadata()->validateTranslations();

        if ($error !== null) {
            $errors->add($error);
        }

        return $errors;
    }
}
