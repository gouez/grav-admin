<?php declare(strict_types=1);

namespace Laser\Core\Content\Test\Media\File;

use PHPUnit\Framework\TestCase;
use Laser\Core\Content\Media\File\FileNameProvider;
use Laser\Core\Content\Test\Media\MediaFixtures;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;

/**
 * @internal
 */
class FileNameProviderTest extends TestCase
{
    use IntegrationTestBehaviour;
    use MediaFixtures;

    /**
     * @var FileNameProvider
     */
    private $nameProvider;

    private Context $context;

    protected function setUp(): void
    {
        $this->nameProvider = $this->getContainer()->get(FileNameProvider::class);

        $this->context = Context::createDefaultContext();

        $this->setFixtureContext($this->context);
    }

    public function testItReturnsOriginalName(): void
    {
        $media = $this->getEmptyMedia();
        $original = 'test';

        $new = $this->nameProvider->provide($original, 'png', $media->getId(), $this->context);

        static::assertEquals($original, $new);
    }

    public function testItGeneratesNewName(): void
    {
        $existing = $this->getJpg();

        $media = $this->getEmptyMedia();

        $new = $this->nameProvider->provide(
            $existing->getFileName(),
            $existing->getFileExtension(),
            $media->getId(),
            $this->context
        );

        static::assertEquals($existing->getFileName() . '_(1)', $new);
    }

    public function testItReturnsOriginalNameOnNewExtension(): void
    {
        $existing = $this->getJpg();

        $media = $this->getEmptyMedia();

        $new = $this->nameProvider->provide(
            $existing->getFileName(),
            'png',
            $media->getId(),
            $this->context
        );

        static::assertEquals($existing->getFileName(), $new);
    }

    public function testItGeneratesNewNameWithoutMediaEntity(): void
    {
        $existing = $this->getJpg();

        $new = $this->nameProvider->provide(
            $existing->getFileName(),
            $existing->getFileExtension(),
            null,
            $this->context
        );

        static::assertEquals($existing->getFileName() . '_(1)', $new);
    }
}
