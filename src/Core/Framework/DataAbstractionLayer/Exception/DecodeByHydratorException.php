<?php
declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer\Exception;

use Laser\Core\Framework\DataAbstractionLayer\Field\Field;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;

#[Package('core')]
class DecodeByHydratorException extends LaserHttpException
{
    public function __construct(Field $field)
    {
        parent::__construct(
            'Decoding of {{ fieldClass }} is handled by the entity hydrator.',
            ['fieldClass' => $field::class]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__DECODING_HANDLED_BY_ENTITY_HYDRATOR';
    }
}
