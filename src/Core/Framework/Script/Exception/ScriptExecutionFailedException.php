<?php declare(strict_types=1);

namespace Laser\Core\Framework\Script\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('core')]
class ScriptExecutionFailedException extends LaserHttpException
{
    private readonly ?\Throwable $rootException;

    public function __construct(
        string $hook,
        string $scriptName,
        \Throwable $previous
    ) {
        $this->rootException = $previous->getPrevious();
        parent::__construct(sprintf(
            'Execution of script "%s" for Hook "%s" failed with message: %s',
            $scriptName,
            $hook,
            $previous->getMessage()
        ), [], $previous);
    }

    public function getStatusCode(): int
    {
        if ($this->rootException instanceof LaserHttpException) {
            return $this->rootException->getStatusCode();
        }

        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    public function getErrorCode(): string
    {
        if ($this->rootException instanceof LaserHttpException) {
            return $this->rootException->getErrorCode();
        }

        return 'FRAMEWORK_SCRIPT_EXECUTION_FAILED';
    }
}
