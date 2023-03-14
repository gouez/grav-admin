<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\Adapter\Twig;

use PHPUnit\Framework\TestCase;
use Laser\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * @internal
 */
class DisabledTwigCacheWarmupTest extends TestCase
{
    use KernelTestBehaviour;

    public function testServiceIsRemoved(): void
    {
        static::expectException(ServiceNotFoundException::class);
        $this->getContainer()->get('twig.template_cache_warmer');
    }
}
