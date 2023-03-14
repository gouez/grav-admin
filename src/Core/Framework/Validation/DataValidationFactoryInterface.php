<?php declare(strict_types=1);

namespace Laser\Core\Framework\Validation;

use Laser\Core\Framework\Log\Package;
use Laser\Core\System\Annotation\Concept\DeprecationPattern\ReplaceDecoratedInterface;
use Laser\Core\System\SalesChannel\SalesChannelContext;

/**
 * @ReplaceDecoratedInterface(
 *     deprecatedInterface="ValidationServiceInterface",
 *     replacedBy="DataValidationFactoryInterface"
 * )
 */
#[Package('core')]
interface DataValidationFactoryInterface
{
    public function create(SalesChannelContext $context): DataValidationDefinition;

    public function update(SalesChannelContext $context): DataValidationDefinition;
}
