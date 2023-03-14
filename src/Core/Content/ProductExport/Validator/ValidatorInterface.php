<?php declare(strict_types=1);

namespace Laser\Core\Content\ProductExport\Validator;

use Laser\Core\Content\ProductExport\Error\ErrorCollection;
use Laser\Core\Content\ProductExport\ProductExportEntity;
use Laser\Core\Framework\Log\Package;

#[Package('sales-channel')]
interface ValidatorInterface
{
    public function validate(ProductExportEntity $productExportEntity, string $productExportContent, ErrorCollection $errors): void;
}
