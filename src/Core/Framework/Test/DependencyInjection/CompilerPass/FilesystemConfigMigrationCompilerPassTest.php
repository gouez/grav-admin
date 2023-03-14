<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\DependencyInjection\CompilerPass;

use PHPUnit\Framework\TestCase;
use Laser\Core\Framework\DependencyInjection\CompilerPass\FilesystemConfigMigrationCompilerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @internal
 */
class FilesystemConfigMigrationCompilerPassTest extends TestCase
{
    private ContainerBuilder $builder;

    public function setUp(): void
    {
        $this->builder = new ContainerBuilder();
        $this->builder->addCompilerPass(new FilesystemConfigMigrationCompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 0);
        $this->builder->setParameter('laser.filesystem.public', []);
        $this->builder->setParameter('laser.filesystem.public.type', 'local');
        $this->builder->setParameter('laser.filesystem.public.config', []);
        $this->builder->setParameter('laser.cdn.url', 'http://test.de');
    }

    public function testConfigMigration(): void
    {
        $this->builder->compile(false);

        static::assertSame($this->builder->getParameter('laser.filesystem.public'), $this->builder->getParameter('laser.filesystem.theme'));
        static::assertSame($this->builder->getParameter('laser.filesystem.public'), $this->builder->getParameter('laser.filesystem.asset'));
        static::assertSame($this->builder->getParameter('laser.filesystem.public'), $this->builder->getParameter('laser.filesystem.sitemap'));

        static::assertSame($this->builder->getParameter('laser.filesystem.public.type'), $this->builder->getParameter('laser.filesystem.theme.type'));
        static::assertSame($this->builder->getParameter('laser.filesystem.public.type'), $this->builder->getParameter('laser.filesystem.asset.type'));
        static::assertSame($this->builder->getParameter('laser.filesystem.public.type'), $this->builder->getParameter('laser.filesystem.sitemap.type'));

        static::assertSame($this->builder->getParameter('laser.filesystem.public.config'), $this->builder->getParameter('laser.filesystem.theme.config'));
        static::assertSame($this->builder->getParameter('laser.filesystem.public.config'), $this->builder->getParameter('laser.filesystem.asset.config'));
        static::assertSame($this->builder->getParameter('laser.filesystem.public.config'), $this->builder->getParameter('laser.filesystem.sitemap.config'));

        // We cannot inherit them, cause they use always in 6.2 the shop url instead the configured one
        static::assertSame('', $this->builder->getParameter('laser.filesystem.theme.url'));
        static::assertSame('', $this->builder->getParameter('laser.filesystem.asset.url'));
        static::assertSame('', $this->builder->getParameter('laser.filesystem.sitemap.url'));
    }

    public function testSetCustomConfigForTheme(): void
    {
        $this->builder->setParameter('laser.filesystem.theme', ['foo' => 'foo']);
        $this->builder->setParameter('laser.filesystem.theme.type', 'amazon-s3');
        $this->builder->setParameter('laser.filesystem.theme.config', ['test' => 'test']);
        $this->builder->setParameter('laser.filesystem.theme.url', 'http://cdn.de');

        $this->builder->compile(false);

        static::assertNotSame($this->builder->getParameter('laser.filesystem.public'), $this->builder->getParameter('laser.filesystem.theme'));
        static::assertNotSame($this->builder->getParameter('laser.filesystem.public.type'), $this->builder->getParameter('laser.filesystem.theme.type'));
        static::assertNotSame($this->builder->getParameter('laser.filesystem.public.config'), $this->builder->getParameter('laser.filesystem.theme.config'));

        static::assertSame('amazon-s3', $this->builder->getParameter('laser.filesystem.theme.type'));
        static::assertSame('http://cdn.de', $this->builder->getParameter('laser.filesystem.theme.url'));
        static::assertSame(['test' => 'test'], $this->builder->getParameter('laser.filesystem.theme.config'));

        static::assertSame($this->builder->getParameter('laser.filesystem.public'), $this->builder->getParameter('laser.filesystem.asset'));
        static::assertSame($this->builder->getParameter('laser.filesystem.public.type'), $this->builder->getParameter('laser.filesystem.asset.type'));
        static::assertSame($this->builder->getParameter('laser.filesystem.public.config'), $this->builder->getParameter('laser.filesystem.asset.config'));

        static::assertSame($this->builder->getParameter('laser.filesystem.public'), $this->builder->getParameter('laser.filesystem.sitemap'));
        static::assertSame($this->builder->getParameter('laser.filesystem.public.type'), $this->builder->getParameter('laser.filesystem.sitemap.type'));
        static::assertSame($this->builder->getParameter('laser.filesystem.public.config'), $this->builder->getParameter('laser.filesystem.sitemap.config'));
    }
}
