<?php declare(strict_types=1);

namespace Laser\Core\Content\Seo\Validation;

use Laser\Core\Content\Seo\SeoUrlRoute\SeoUrlRouteConfig;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Validation\DataValidationDefinition;

#[Package('sales-channel')]
interface SeoUrlDataValidationFactoryInterface
{
    public function buildValidation(Context $context, SeoUrlRouteConfig $config): DataValidationDefinition;
}
