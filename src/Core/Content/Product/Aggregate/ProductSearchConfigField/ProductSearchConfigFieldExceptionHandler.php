<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\Aggregate\ProductSearchConfigField;

use Laser\Core\Content\Product\Exception\DuplicateProductSearchConfigFieldException;
use Laser\Core\Framework\DataAbstractionLayer\Dbal\ExceptionHandlerInterface;
use Laser\Core\Framework\Log\Package;

#[Package('inventory')]
class ProductSearchConfigFieldExceptionHandler implements ExceptionHandlerInterface
{
    public function getPriority(): int
    {
        return ExceptionHandlerInterface::PRIORITY_DEFAULT;
    }

    public function matchException(\Exception $e): ?\Exception
    {
        if (preg_match('/SQLSTATE\[23000\]:.*1062 Duplicate.*uniq.search_config_field.field__config_id\'/', $e->getMessage())) {
            $field = [];
            preg_match('/Duplicate entry \'(.*)\' for key/', $e->getMessage(), $field);
            $field = substr($field[1], 0, (int) strpos($field[1], '-'));

            return new DuplicateProductSearchConfigFieldException($field, $e);
        }

        return null;
    }
}
