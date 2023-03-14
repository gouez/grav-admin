<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\Store\Api;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Laser\Core\Framework\Store\Api\ExtensionStoreDataController;
use Laser\Core\Framework\Test\Store\ExtensionBehaviour;
use Laser\Core\Framework\Test\Store\StoreClientBehaviour;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;

/**
 * @internal
 */
class ExtensionStoreDataControllerTest extends TestCase
{
    use IntegrationTestBehaviour;
    use StoreClientBehaviour;
    use ExtensionBehaviour;

    /**
     * @var ExtensionStoreDataController
     */
    private $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = $this->getContainer()->get(ExtensionStoreDataController::class);
    }

    public function testInstalled(): void
    {
        $this->installApp(__DIR__ . '/../_fixtures/TestApp');

        $this->getRequestHandler()->reset();
        $this->getRequestHandler()->append(new Response(200, [], '[]'));

        $response = $this->controller->getInstalledExtensions($this->createAdminStoreContext());
        $data = json_decode($response->getContent(), true, 512, \JSON_THROW_ON_ERROR);

        static::assertNotEmpty($data);
        static::assertContains('TestApp', array_column($data, 'name'));
    }
}
