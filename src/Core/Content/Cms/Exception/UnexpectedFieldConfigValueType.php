<?php declare(strict_types=1);

namespace Laser\Core\Content\Cms\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;

#[Package('content')]
class UnexpectedFieldConfigValueType extends LaserHttpException
{
    public function __construct(
        string $fieldConfigName,
        string $expectedType,
        string $givenType
    ) {
        parent::__construct(
            'Expected to load value of "{{ fieldConfigName }}" with type "{{ expectedType }}", but value with type "{{ givenType }}" given.',
            [
                'fieldConfigName' => $fieldConfigName,
                'expectedType' => $expectedType,
                'givenType' => $givenType,
            ]
        );
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__CMS_UNEXPECTED_VALUE_TYPE';
    }
}
