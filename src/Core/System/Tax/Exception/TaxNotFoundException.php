<?php declare(strict_types=1);

namespace Laser\Core\System\Tax\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('customer-order')]
class TaxNotFoundException extends LaserHttpException
{
    public function __construct(string $taxId)
    {
        parent::__construct(
            'Tax with id "{{ id }}" not found.',
            ['id' => $taxId]
        );
    }

    public function getErrorCode(): string
    {
        return 'SYSTEM__TAX_NOT_FOUND';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
