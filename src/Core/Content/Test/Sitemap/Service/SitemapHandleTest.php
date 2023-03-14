<?php declare(strict_types=1);

namespace Laser\Core\Content\Test\Sitemap\Service;

use League\Flysystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Laser\Core\Content\Category\CategoryEntity;
use Laser\Core\Content\Sitemap\Service\SitemapHandle;
use Laser\Core\Content\Sitemap\Struct\Url;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\System\SalesChannel\SalesChannelContext;

/**
 * @internal
 */
#[Package('sales-channel')]
class SitemapHandleTest extends TestCase
{
    use KernelTestBehaviour;

    private ?SitemapHandle $handle = null;

    public function testWriteWithoutFinish(): void
    {
        $url = new Url();
        $url->setLoc('https://laser.com');
        $url->setLastmod(new \DateTime());
        $url->setChangefreq('weekly');
        $url->setResource(CategoryEntity::class);
        $url->setIdentifier(Uuid::randomHex());

        $fileSystem = $this->createMock(Filesystem::class);
        $fileSystem->expects(static::never())->method('write');

        $this->handle = new SitemapHandle(
            $fileSystem,
            $this->getContext(),
            $this->getContainer()->get('event_dispatcher')
        );

        $this->handle->write([
            $url,
        ]);
    }

    public function testWrite(): void
    {
        $url = new Url();
        $url->setLoc('https://laser.com');
        $url->setLastmod(new \DateTime());
        $url->setChangefreq('weekly');
        $url->setResource(CategoryEntity::class);
        $url->setIdentifier(Uuid::randomHex());

        $fileSystem = $this->createMock(Filesystem::class);
        $fileSystem->expects(static::once())->method('write');

        $this->handle = new SitemapHandle(
            $fileSystem,
            $this->getContext(),
            $this->getContainer()->get('event_dispatcher')
        );

        $this->handle->write([$url]);
        $this->handle->finish();
    }

    public function testWrite101kItems(): void
    {
        $url = new Url();
        $url->setLoc('https://laser.com');
        $url->setLastmod(new \DateTime());
        $url->setChangefreq('weekly');
        $url->setResource(CategoryEntity::class);
        $url->setIdentifier(Uuid::randomHex());

        $list = [];

        for ($i = 1; $i <= 101000; ++$i) {
            $list[] = clone $url;
        }

        $fileSystem = $this->createMock(Filesystem::class);
        $fileSystem->expects(static::atLeast(3))->method('write');

        $this->handle = new SitemapHandle(
            $fileSystem,
            $this->getContext(),
            $this->getContainer()->get('event_dispatcher')
        );

        $this->handle->write($list);
        $this->handle->finish();
    }

    private function getContext(): SalesChannelContext
    {
        return $this->createMock(SalesChannelContext::class);
    }
}
