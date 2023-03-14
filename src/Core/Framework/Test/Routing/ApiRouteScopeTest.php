<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\Routing;

use PHPUnit\Framework\TestCase;
use Laser\Core\Framework\Api\Context\AdminApiSource;
use Laser\Core\Framework\Api\Context\ContextSource;
use Laser\Core\Framework\Api\Context\SalesChannelApiSource;
use Laser\Core\Framework\Api\Context\SystemSource;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Routing\ApiRouteScope;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\PlatformRequest;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
class ApiRouteScopeTest extends TestCase
{
    use IntegrationTestBehaviour;

    public static function provideAllowedData()
    {
        return [
            [new AdminApiSource(null, null), true],
            [new AdminApiSource(null, null), false],
            [new SystemSource(), false],
        ];
    }

    public static function provideForbiddenData()
    {
        return [
            [new SalesChannelApiSource(Uuid::randomHex()), true],
            [new SystemSource(), true],
        ];
    }

    /**
     * @dataProvider provideAllowedData
     */
    public function testAllowedCombinations(ContextSource $source, bool $authRequired): void
    {
        $scope = $this->getContainer()->get(ApiRouteScope::class);

        $request = Request::create('/api/foo');
        $request->attributes->set(PlatformRequest::ATTRIBUTE_CONTEXT_OBJECT, Context::createDefaultContext($source));
        $request->attributes->set('auth_required', $authRequired);

        static::assertTrue($scope->isAllowedPath($request->getPathInfo()));
        static::assertTrue($scope->isAllowed($request));
    }

    /**
     * @dataProvider provideForbiddenData
     */
    public function testForbiddenCombinations(ContextSource $source, bool $authRequired): void
    {
        $scope = $this->getContainer()->get(ApiRouteScope::class);

        $request = Request::create('/api/foo');
        $request->attributes->set(PlatformRequest::ATTRIBUTE_CONTEXT_OBJECT, Context::createDefaultContext($source));
        $request->attributes->set('auth_required', $authRequired);

        static::assertFalse($scope->isAllowed($request));
    }
}
