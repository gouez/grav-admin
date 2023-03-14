<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\Asset;

use PHPUnit\Framework\TestCase;
use Laser\Core\Framework\Adapter\Asset\FallbackUrlPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

/**
 * @internal
 */
class FallbackUrlPackageTest extends TestCase
{
    public function testCliFallbacksToAppUrl(): void
    {
        $package = new FallbackUrlPackage([''], new EmptyVersionStrategy());
        $url = $package->getUrl('test');

        static::assertSame($_SERVER['APP_URL'] . '/test', $url);
    }

    public function testCliUrlGiven(): void
    {
        $package = new FallbackUrlPackage(['http://laser.com'], new EmptyVersionStrategy());
        $url = $package->getUrl('test');

        static::assertSame('http://laser.com/test', $url);
    }

    public function testWebFallbackToRequest(): void
    {
        $_SERVER['HTTP_HOST'] = 'test.de';
        $package = new FallbackUrlPackage([''], new EmptyVersionStrategy());
        $url = $package->getUrl('test');

        static::assertSame('http://test.de/test', $url);
        unset($_SERVER['HTTP_HOST']);
    }
}
