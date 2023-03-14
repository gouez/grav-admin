<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Promotion\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('checkout')]
class SetGroupNotFoundException extends LaserHttpException
{
    public function __construct(string $groupId)
    {
        parent::__construct('Promotion SetGroup "{{ id }}" has not been found!', ['id' => $groupId]);
    }

    public function getErrorCode(): string
    {
        return 'CHECKOUT__PROMOTION_SETGROUP_NOT_FOUND';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
