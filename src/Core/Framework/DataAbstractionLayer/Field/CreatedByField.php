<?php declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer\Field;

use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\FieldSerializer\CreatedByFieldSerializer;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\User\UserDefinition;

#[Package('core')]
class CreatedByField extends FkField
{
    public function __construct(private readonly array $allowedWriteScopes = [Context::SYSTEM_SCOPE])
    {
        parent::__construct('created_by_id', 'createdById', UserDefinition::class);
    }

    public function getAllowedWriteScopes(): array
    {
        return $this->allowedWriteScopes;
    }

    protected function getSerializerClass(): string
    {
        return CreatedByFieldSerializer::class;
    }
}
