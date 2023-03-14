<?php declare(strict_types=1);

namespace Laser\Core\Content\Sitemap\Service;

use League\Flysystem\FilesystemOperator;
use Laser\Core\Content\Sitemap\Struct\Sitemap;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\Asset\Package;

#[\Laser\Core\Framework\Log\Package('sales-channel')]
class SitemapLister implements SitemapListerInterface
{
    /**
     * @internal
     */
    public function __construct(
        private readonly FilesystemOperator $filesystem,
        private readonly Package $package
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getSitemaps(SalesChannelContext $salesChannelContext): array
    {
        $files = $this->filesystem->listContents('sitemap/salesChannel-' . $salesChannelContext->getSalesChannel()->getId() . '-' . $salesChannelContext->getLanguageId());

        $sitemaps = [];

        foreach ($files as $file) {
            if ($file->isDir()) {
                continue;
            }

            $sitemaps[] = new Sitemap($this->package->getUrl($file->path()), 0, new \DateTime('@' . ($file->lastModified() ?? time())));
        }

        return $sitemaps;
    }
}
