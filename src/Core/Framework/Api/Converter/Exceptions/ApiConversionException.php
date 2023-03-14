<?php declare(strict_types=1);

namespace Laser\Core\Framework\Api\Converter\Exceptions;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserException;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('core')]
class ApiConversionException extends LaserHttpException
{
    /**
     * @param array<string, \Throwable[]> $exceptions
     */
    public function __construct(private array $exceptions = [])
    {
        parent::__construct('Api Version conversion failed, got {{ numberOfFailures }} failure(s).', ['numberOfFailures' => \count($exceptions)]);
    }

    public function add(\Throwable $exception, string $pointer): void
    {
        $this->exceptions[$pointer][] = $exception;
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function tryToThrow(): void
    {
        if (empty($this->exceptions)) {
            return;
        }

        throw $this;
    }

    public function getErrors(bool $withTrace = false): \Generator
    {
        foreach ($this->exceptions as $pointer => $innerExceptions) {
            /** @var LaserException $exception */
            foreach ($innerExceptions as $exception) {
                $parameters = [];
                $errorCode = 0;

                if ($exception instanceof LaserException) {
                    $parameters = $exception->getParameters();
                    $errorCode = $exception->getErrorCode();
                }

                $error = [
                    'status' => (string) $this->getStatusCode(),
                    'code' => $errorCode,
                    'title' => Response::$statusTexts[Response::HTTP_BAD_REQUEST],
                    'detail' => $exception->getMessage(),
                    'source' => ['pointer' => $pointer],
                    'meta' => [
                        'parameters' => $parameters,
                    ],
                ];

                if ($withTrace) {
                    $error['trace'] = $exception->getTraceAsString();
                }

                yield $error;
            }
        }
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__API_VERSION_CONVERSION';
    }
}
