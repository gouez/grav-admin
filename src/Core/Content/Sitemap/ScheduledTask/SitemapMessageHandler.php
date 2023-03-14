<?php declare(strict_types=1);

namespace Laser\Core\Content\Sitemap\ScheduledTask;

use Psr\Log\LoggerInterface;
use Laser\Core\Content\Sitemap\Exception\AlreadyLockedException;
use Laser\Core\Content\Sitemap\Service\SitemapExporterInterface;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\Context\AbstractSalesChannelContextFactory;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextService;
use Laser\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @internal
 */
#[AsMessageHandler]
#[Package('sales-channel')]
final class SitemapMessageHandler
{
    /**
     * @internal
     */
    public function __construct(
        private readonly AbstractSalesChannelContextFactory $salesChannelContextFactory,
        private readonly SitemapExporterInterface $sitemapExporter,
        private readonly LoggerInterface $logger,
        private readonly SystemConfigService $systemConfigService,
    ) {
    }

    public function __invoke(SitemapMessage $message): void
    {
        $sitemapRefreshStrategy = $this->systemConfigService->getInt('core.sitemap.sitemapRefreshStrategy');
        if ($sitemapRefreshStrategy !== SitemapExporterInterface::STRATEGY_SCHEDULED_TASK) {
            return;
        }

        $this->generate($message);
    }

    private function generate(SitemapMessage $message): void
    {
        if ($message->getLastSalesChannelId() === null || $message->getLastLanguageId() === null) {
            return;
        }

        $context = $this->salesChannelContextFactory->create('', $message->getLastSalesChannelId(), [SalesChannelContextService::LANGUAGE_ID => $message->getLastLanguageId()]);

        try {
            $this->sitemapExporter->generate($context, true, $message->getLastProvider(), $message->getNextOffset());
        } catch (AlreadyLockedException $exception) {
            $this->logger->error(sprintf('ERROR: %s', $exception->getMessage()));
        }
    }
}
