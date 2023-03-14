<?php declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer\Exception;

use Laser\Core\Framework\DataAbstractionLayer\Write\Command\WriteCommand;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;

#[Package('core')]
class UnsupportedCommandTypeException extends LaserHttpException
{
    public function __construct(WriteCommand $command)
    {
        parent::__construct(
            'Command of class {{ command }} is not supported by {{ definition }}',
            ['command' => $command::class, 'definition' => $command->getDefinition()->getEntityName()]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__UNSUPPORTED_COMMAND_TYPE_EXCEPTION';
    }
}
