<?php
declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer\Exception;

use Laser\Core\Framework\DataAbstractionLayer\Field\Field;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('core')]
class WriteNotSupportedException extends LaserHttpException
{
    private readonly Field $field;

    public function __construct(Field $field)
    {
        parent::__construct(
            'Writing to ReadOnly field "{{ field }}" is not supported.',
            ['field' => $field::class]
        );

        $this->field = $field;
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function getField(): Field
    {
        return $this->field;
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__WRITE_NOT_SUPPORTED';
    }
}
