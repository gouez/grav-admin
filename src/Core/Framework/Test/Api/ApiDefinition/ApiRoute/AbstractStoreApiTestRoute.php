<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\Api\ApiDefinition\ApiRoute;

/**
 * @internal
 */
abstract class AbstractStoreApiTestRoute
{
    abstract public function getDecorated(): AbstractStoreApiTestRoute;
}
