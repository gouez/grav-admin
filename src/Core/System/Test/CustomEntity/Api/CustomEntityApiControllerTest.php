<?php declare(strict_types=1);

namespace Laser\Core\System\Test\CustomEntity\Api;

use PHPUnit\Framework\TestCase;
use Laser\Core\Framework\Test\TestCaseBase\AdminApiTestBehaviour;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Laser\Core\System\Test\CustomEntity\CustomEntityTest;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 */
class CustomEntityApiControllerTest extends TestCase
{
    use IntegrationTestBehaviour;
    use AdminApiTestBehaviour;

    /**
     * All other cases are covered in @see CustomEntityTest
     * as they need a complex and time-consuming setup, because they need DB schema updates
     */
    public function testSearchOnNonExistingCustomEntitiesResultsIn404(): void
    {
        $browser = $this->getBrowser();
        $browser->request('POST', '/api/search/custom-entity-non-existing');

        static::assertEquals(Response::HTTP_NOT_FOUND, $browser->getResponse()->getStatusCode(), $browser->getResponse()->getContent());
    }
}
