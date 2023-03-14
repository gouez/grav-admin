<?php declare(strict_types=1);

namespace Laser\Core\Framework\App\Validation;

use Laser\Core\Framework\App\Exception\AppValidationException;
use Laser\Core\Framework\App\Manifest\Manifest;
use Laser\Core\Framework\App\Validation\Error\ErrorCollection;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Log\Package;

/**
 * @internal only for use by the app-system, will be considered internal from v6.4.0 onward
 */
#[Package('core')]
class ManifestValidator
{
    /**
     * @param AbstractManifestValidator[] $validators
     */
    public function __construct(private readonly iterable $validators)
    {
    }

    public function validate(Manifest $manifest, Context $context): void
    {
        $errors = new ErrorCollection();
        foreach ($this->validators as $validator) {
            $errors->addErrors($validator->validate($manifest, $context));
        }

        if ($errors->count() === 0) {
            return;
        }

        throw new AppValidationException($manifest->getMetadata()->getName(), $errors);
    }
}
