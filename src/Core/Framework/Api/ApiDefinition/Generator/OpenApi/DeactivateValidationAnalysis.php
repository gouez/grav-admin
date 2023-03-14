<?php declare(strict_types=1);

namespace Laser\Core\Framework\Api\ApiDefinition\Generator\OpenApi;

use OpenApi\Analysis;
use Laser\Core\Framework\Log\Package;

#[Package('core')]
class DeactivateValidationAnalysis extends Analysis
{
    public function validate(): bool
    {
        return false;
        //deactivate Validitation
    }
}
