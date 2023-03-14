<?php declare(strict_types=1);

namespace Laser\Core\Content\ProductExport\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('sales-channel')]
class EmptyExportException extends LaserHttpException
{
    public function __construct(?string $id = null)
    {
        if (empty($id)) {
            parent::__construct('No products for export found');
        } else {
            parent::__construct('No products for export with ID {{ id }} found', ['id' => $id]);
        }
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__PRODUCT_EXPORT_EMPTY';
    }
}
