<?php declare(strict_types=1);

namespace Laser\Core\Content\Sitemap\Provider;

use Laser\Core\Content\Sitemap\Service\ConfigHandler;
use Laser\Core\Content\Sitemap\Struct\Url;
use Laser\Core\Content\Sitemap\Struct\UrlResult;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Plugin\Exception\DecorationPatternException;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('sales-channel')]
class CustomUrlProvider extends AbstractUrlProvider
{
    /**
     * @internal
     */
    public function __construct(private readonly ConfigHandler $configHandler)
    {
    }

    public function getDecorated(): AbstractUrlProvider
    {
        throw new DecorationPatternException(self::class);
    }

    public function getName(): string
    {
        return 'custom';
    }

    /**
     * {@inheritdoc}
     */
    public function getUrls(SalesChannelContext $context, int $limit, ?int $offset = null): UrlResult
    {
        $sitemapCustomUrls = $this->configHandler->get(ConfigHandler::CUSTOM_URLS_KEY);

        $urls = [];
        $url = new Url();
        foreach ($sitemapCustomUrls as $sitemapCustomUrl) {
            if (!$this->isAvailableForSalesChannel($sitemapCustomUrl, $context->getSalesChannel()->getId())) {
                continue;
            }

            $newUrl = clone $url;
            $newUrl->setLoc($sitemapCustomUrl['url']);
            $newUrl->setLastmod($sitemapCustomUrl['lastMod']);
            $newUrl->setChangefreq($sitemapCustomUrl['changeFreq']);
            $newUrl->setResource('custom');
            $newUrl->setIdentifier('');

            $urls[] = $newUrl;
        }

        return new UrlResult($urls, null);
    }

    private function isAvailableForSalesChannel(array $url, ?string $salesChannelId): bool
    {
        return \in_array($url['salesChannelId'], [$salesChannelId, null], true);
    }
}
