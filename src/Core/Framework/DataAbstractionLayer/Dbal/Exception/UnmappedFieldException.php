<?php declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer\Dbal\Exception;

use Laser\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('core')]
class UnmappedFieldException extends LaserHttpException
{
    public function __construct(
        string $field,
        EntityDefinition $definition
    ) {
        $fieldParts = explode('.', $field);
        $name = array_pop($fieldParts);

        parent::__construct(
            'Field "{{ field }}" in entity "{{ entity }}" was not found.',
            ['field' => $name, 'entity' => $definition->getEntityName()]
        );
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__UNMAPPED_FIELD';
    }
}
