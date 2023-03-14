<?php declare(strict_types=1);

namespace Laser\Core\System\SystemConfig\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;

#[Package('system-settings')]
class XmlElementNotFoundException extends LaserHttpException
{
    public function __construct(string $element)
    {
        parent::__construct(
            'Unable to locate element with the name "{{ element }}".',
            ['element' => $element]
        );
    }

    public function getErrorCode(): string
    {
        return 'SYSTEM__XML_ELEMENT_NOT_FOUND';
    }
}
