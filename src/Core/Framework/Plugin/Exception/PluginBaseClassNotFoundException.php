<?php declare(strict_types=1);

namespace Laser\Core\Framework\Plugin\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('core')]
class PluginBaseClassNotFoundException extends LaserHttpException
{
    public function __construct(string $baseClass)
    {
        parent::__construct(
            'The class "{{ baseClass }}" is not found. Probably an class loader error. Check your plugin composer.json',
            ['baseClass' => $baseClass]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__PLUGIN_BASE_CLASS_NOT_FOUND';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
