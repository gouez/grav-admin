<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\Plugin;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Laser\Core\Framework\Adapter\Cache\CacheClearer;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Plugin\KernelPluginLoader\StaticKernelPluginLoader;
use Laser\Core\Framework\Plugin\PluginExtractor;
use Laser\Core\Framework\Plugin\PluginManagementService;
use Laser\Core\Framework\Plugin\PluginService;
use Laser\Core\Framework\Plugin\PluginZipDetector;
use Laser\Core\Framework\Plugin\Util\PluginFinder;
use Laser\Core\Framework\Test\TestCaseBase\KernelLifecycleManager;
use Laser\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\Kernel;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @internal
 *
 * @group slow
 * @group skip-paratest
 */
class PluginManagementServiceTest extends TestCase
{
    use KernelTestBehaviour;
    use PluginTestsHelper;

    private const TEST_ZIP_NAME = 'SwagFashionTheme.zip';
    private const FIXTURE_PATH = __DIR__ . '/_fixture/';
    private const PLUGIN_ZIP_FIXTURE_PATH = self::FIXTURE_PATH . self::TEST_ZIP_NAME;
    private const PLUGINS_PATH = self::FIXTURE_PATH . 'plugins';
    private const PLUGIN_FASHION_THEME_PATH = self::PLUGINS_PATH . '/SwagFashionTheme';
    private const PLUGIN_FASHION_THEME_BASE_CLASS_PATH = self::PLUGIN_FASHION_THEME_PATH . '/SwagFashionTheme.php';

    /**
     * @var Filesystem
     */
    private $filesystem;

    private string $cacheDir;

    protected function setUp(): void
    {
        $this->filesystem = $this->getContainer()->get(Filesystem::class);

        $this->cacheDir = $this->createTestCacheDirectory();

        $this->filesystem->copy(
            self::FIXTURE_PATH . 'archives/' . self::TEST_ZIP_NAME,
            self::PLUGIN_ZIP_FIXTURE_PATH
        );
    }

    protected function tearDown(): void
    {
        $this->filesystem->remove(self::PLUGIN_FASHION_THEME_PATH);
        $this->filesystem->remove(self::PLUGIN_ZIP_FIXTURE_PATH);
        $this->filesystem->remove($this->cacheDir);

        Kernel::getConnection()->executeStatement('DELETE FROM plugin');
    }

    public function testUploadPlugin(): void
    {
        $pluginFile = $this->createUploadedFile();
        $this->getPluginManagementService()->uploadPlugin($pluginFile, Context::createDefaultContext());

        static::assertFileExists(self::PLUGIN_FASHION_THEME_PATH);
        static::assertFileExists(self::PLUGIN_FASHION_THEME_BASE_CLASS_PATH);
    }

    public function testExtractPluginZip(): void
    {
        $this->getPluginManagementService()->extractPluginZip(self::PLUGIN_ZIP_FIXTURE_PATH);

        $extractedPlugin = $this->filesystem->exists(self::PLUGIN_FASHION_THEME_PATH);
        $extractedPluginBaseClass = $this->filesystem->exists(self::PLUGIN_FASHION_THEME_BASE_CLASS_PATH);
        $pluginZipExists = $this->filesystem->exists(self::PLUGIN_ZIP_FIXTURE_PATH);
        static::assertTrue($extractedPlugin);
        static::assertTrue($extractedPluginBaseClass);
        static::assertFalse($pluginZipExists);
    }

    public function testExtractPluginZipWithoutDeletion(): void
    {
        $this->getPluginManagementService()->extractPluginZip(self::PLUGIN_ZIP_FIXTURE_PATH, false);

        $extractedPlugin = $this->filesystem->exists(self::PLUGIN_FASHION_THEME_PATH);
        $extractedPluginBaseClass = $this->filesystem->exists(self::PLUGIN_FASHION_THEME_BASE_CLASS_PATH);
        $pluginZipExists = $this->filesystem->exists(self::PLUGIN_ZIP_FIXTURE_PATH);
        static::assertTrue($extractedPlugin);
        static::assertTrue($extractedPluginBaseClass);
        static::assertTrue($pluginZipExists);
    }

    private function createTestCacheDirectory(): string
    {
        $kernelClass = KernelLifecycleManager::getKernelClass();
        /** @var Kernel $newTestKernel */
        $newTestKernel = new $kernelClass(
            'test',
            true,
            new StaticKernelPluginLoader(KernelLifecycleManager::getClassLoader()),
            Uuid::randomHex(),
            '2.2.2',
            $this->getContainer()->get(Connection::class)
        );

        $newTestKernel->boot();
        $cacheDir = $newTestKernel->getCacheDir();
        $newTestKernel->shutdown();

        return $cacheDir;
    }

    private function createUploadedFile(): UploadedFile
    {
        return new UploadedFile(self::PLUGIN_ZIP_FIXTURE_PATH, self::TEST_ZIP_NAME, null, null, true);
    }

    private function getPluginManagementService(): PluginManagementService
    {
        return new PluginManagementService(
            self::PLUGINS_PATH,
            new PluginZipDetector(),
            new PluginExtractor(['plugin' => self::PLUGINS_PATH], $this->filesystem),
            $this->getPluginService(),
            $this->filesystem,
            $this->getCacheClearer(),
            $this->getContainer()->get('laser.store_download_client')
        );
    }

    private function getPluginService(): PluginService
    {
        return $this->createPluginService(
            __DIR__ . '/_fixture/plugins',
            $this->getContainer()->getParameter('kernel.project_dir'),
            $this->getContainer()->get('plugin.repository'),
            $this->getContainer()->get('language.repository'),
            $this->getContainer()->get(PluginFinder::class)
        );
    }

    private function getCacheClearer(): CacheClearer
    {
        return new CacheClearer(
            [],
            $this->getContainer()->get('cache_clearer'),
            $this->filesystem,
            $this->cacheDir,
            'test',
            $this->getContainer()->get('messenger.bus.laser')
        );
    }
}
