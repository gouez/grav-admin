<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Document\Twig;

use Laser\Core\Checkout\Document\DocumentGenerator\Counter;
use Laser\Core\Checkout\Document\Event\DocumentTemplateRendererParameterEvent;
use Laser\Core\Framework\Adapter\Translation\Translator;
use Laser\Core\Framework\Adapter\Twig\TemplateFinder;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\System\SalesChannel\Context\AbstractSalesChannelContextFactory;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[Package('customer-order')]
class DocumentTemplateRenderer
{
    /**
     * @internal
     */
    public function __construct(
        private readonly TemplateFinder $templateFinder,
        private readonly Environment $twig,
        private readonly Translator $translator,
        private readonly AbstractSalesChannelContextFactory $contextFactory,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    /**
     * @param array<string, mixed> $parameters
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function render(
        string $view,
        array $parameters = [],
        ?Context $context = null,
        ?string $salesChannelId = null,
        ?string $languageId = null,
        ?string $locale = null
    ): string {
        // If parameters for specific language setting provided, inject to translator
        if ($context !== null && $salesChannelId !== null && $languageId !== null && $locale !== null) {
            $this->translator->injectSettings(
                $salesChannelId,
                $languageId,
                $locale,
                $context
            );
            $salesChannelContext = $this->contextFactory->create(
                Uuid::randomHex(),
                $salesChannelId,
                [SalesChannelContextService::LANGUAGE_ID => $languageId]
            );

            $parameters['context'] = $salesChannelContext;
        }

        $documentTemplateRendererParameterEvent = new DocumentTemplateRendererParameterEvent($parameters);
        $this->eventDispatcher->dispatch($documentTemplateRendererParameterEvent);
        $parameters['extensions'] = $documentTemplateRendererParameterEvent->getExtensions();

        $parameters['counter'] = new Counter();

        $view = $this->resolveView($view);

        $rendered = $this->twig->render($view, $parameters);

        // If injected translator reject it
        if ($context !== null && $salesChannelId !== null && $languageId !== null && $locale !== null) {
            $this->translator->resetInjection();
        }

        return $rendered;
    }

    /**
     * @throws LoaderError
     */
    private function resolveView(string $view): string
    {
        $this->templateFinder->reset();

        return $this->templateFinder->find($view);
    }
}
