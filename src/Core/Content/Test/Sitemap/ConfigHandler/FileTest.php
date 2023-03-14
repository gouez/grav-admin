<?php declare(strict_types=1);

namespace Laser\Core\Content\Test\Sitemap\ConfigHandler;

use PHPUnit\Framework\TestCase;
use Laser\Core\Content\Sitemap\ConfigHandler\File;
use Laser\Core\Content\Sitemap\Service\ConfigHandler;
use Laser\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('sales-channel')]
class FileTest extends TestCase
{
    public function testAddLastModDate(): void
    {
        $fileConfigHandler = new File([
            ConfigHandler::EXCLUDED_URLS_KEY => [],
            ConfigHandler::CUSTOM_URLS_KEY => [
                [
                    'url' => 'foo',
                    'changeFreq' => 'weekly',
                    'priority' => 0.5,
                    'salesChannelId' => 2,
                    'lastMod' => '2019-09-27 10:00:00',
                ],
            ],
        ]);

        $customUrl = $fileConfigHandler->getSitemapConfig()[ConfigHandler::CUSTOM_URLS_KEY][0];

        static::assertInstanceOf(\DateTimeInterface::class, $customUrl['lastMod']);
    }
}
