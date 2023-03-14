<?php declare(strict_types=1);

namespace Laser\Core\Framework\Store\Services;

use Psr\Http\Message\ResponseInterface;
use Laser\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('merchant-services')]
interface MiddlewareInterface
{
    public function __invoke(ResponseInterface $response): ResponseInterface;
}
