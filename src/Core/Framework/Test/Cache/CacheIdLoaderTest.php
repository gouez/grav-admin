<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\Cache;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Laser\Core\Framework\Adapter\Cache\CacheIdLoader;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;

/**
 * @internal
 *
 * @group cache
 */
class CacheIdLoaderTest extends TestCase
{
    use IntegrationTestBehaviour;

    /**
     * @var object|CacheIdLoader|null
     */
    private $loader;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loader = $this->getContainer()->get(CacheIdLoader::class);
    }

    public function testLoadExisting(): void
    {
        $id = Uuid::randomHex();

        $connection = $this->createMock(Connection::class);
        $connection->method('fetchOne')
            ->willReturn($id);

        $loader = new CacheIdLoader($connection);

        static::assertSame($id, $loader->load());
    }

    public function testMissingCacheIdWritesId(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('fetchOne')
            ->willReturn(false);

        $connection
            ->expects(static::once())
            ->method('executeStatement');

        $loader = new CacheIdLoader($connection);

        static::assertIsString($loader->load());
    }

    public function testCacheIdIsNotAString(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('fetchOne')
            ->willReturn(0);

        $connection
            ->expects(static::once())
            ->method('executeStatement');

        $loader = new CacheIdLoader($connection);

        static::assertIsString($loader->load());
    }

    public function testCacheIdIsLoadedFromDatabase(): void
    {
        $old = $this->loader->load();
        static::assertIsString($old);

        $new = Uuid::randomHex();
        $this->getContainer()->get(Connection::class)
            ->executeStatement(
                'REPLACE INTO app_config (`key`, `value`) VALUES (:key, :cacheId)',
                ['cacheId' => $new, 'key' => 'cache-id']
            );

        static::assertSame($new, $this->loader->load());

        $this->loader->write($old);

        static::assertSame($old, $this->loader->load());
    }
}
