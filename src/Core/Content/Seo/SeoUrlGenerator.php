<?php declare(strict_types=1);

namespace Laser\Core\Content\Seo;

use Laser\Core\Content\Seo\Exception\InvalidTemplateException;
use Laser\Core\Content\Seo\SeoUrl\SeoUrlEntity;
use Laser\Core\Content\Seo\SeoUrlRoute\SeoUrlMapping;
use Laser\Core\Content\Seo\SeoUrlRoute\SeoUrlRouteConfig;
use Laser\Core\Content\Seo\SeoUrlRoute\SeoUrlRouteInterface;
use Laser\Core\Framework\Adapter\Twig\TwigVariableParser;
use Laser\Core\Framework\Adapter\Twig\TwigVariableParserFactory;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\Dbal\Common\RepositoryIterator;
use Laser\Core\Framework\DataAbstractionLayer\Dbal\EntityDefinitionQueryHelper;
use Laser\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Laser\Core\Framework\DataAbstractionLayer\Entity;
use Laser\Core\Framework\DataAbstractionLayer\EntityCollection;
use Laser\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Laser\Core\Framework\DataAbstractionLayer\Field\Field;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\Runtime;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelEntity;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Twig\Error\SyntaxError;
use Twig\Loader\ArrayLoader;

#[Package('sales-channel')]
class SeoUrlGenerator
{
    final public const ESCAPE_SLUGIFY = 'slugifyurlencode';

    private readonly TwigVariableParser $twigVariableParser;

    /**
     * @internal
     */
    public function __construct(
        private readonly DefinitionInstanceRegistry $definitionRegistry,
        private readonly RouterInterface $router,
        private readonly RequestStack $requestStack,
        private readonly Environment $twig,
        TwigVariableParserFactory $parserFactory
    ) {
        $this->twigVariableParser = $parserFactory->getParser($twig);
    }

    /**
     * @param array<string|array<string, string>> $ids
     *
     * @return iterable<SeoUrlEntity>
     */
    public function generate(array $ids, string $template, SeoUrlRouteInterface $route, Context $context, SalesChannelEntity $salesChannel): iterable
    {
        $criteria = new Criteria($ids);
        $route->prepareCriteria($criteria, $salesChannel);

        $config = $route->getConfig();

        $repository = $this->definitionRegistry->getRepository($config->getDefinition()->getEntityName());

        $associations = $this->getAssociations($template, $repository->getDefinition());
        $criteria->addAssociations($associations);

        $criteria->setLimit(50);

        /** @var RepositoryIterator $iterator */
        $iterator = $context->enableInheritance(static fn (Context $context) => new RepositoryIterator($repository, $context, $criteria));

        $this->setTwigTemplate($config, $template);

        while ($entities = $iterator->fetch()) {
            yield from $this->generateUrls($route, $config, $salesChannel, $entities);
        }
    }

    /**
     * @param EntityCollection<Entity> $entities
     *
     * @return iterable<SeoUrlEntity>
     */
    private function generateUrls(SeoUrlRouteInterface $seoUrlRoute, SeoUrlRouteConfig $config, SalesChannelEntity $salesChannel, EntityCollection $entities): iterable
    {
        $request = $this->requestStack->getMainRequest();

        $basePath = $request ? $request->getBasePath() : '';

        /** @var Entity $entity */
        foreach ($entities as $entity) {
            $seoUrl = new SeoUrlEntity();
            $seoUrl->setForeignKey($entity->getUniqueIdentifier());

            $seoUrl->setIsCanonical(true);
            $seoUrl->setIsModified(false);
            $seoUrl->setIsDeleted(false);

            $copy = clone $seoUrl;

            $mapping = $seoUrlRoute->getMapping($entity, $salesChannel);

            $copy->setError($mapping->getError());
            $pathInfo = $this->router->generate($config->getRouteName(), $mapping->getInfoPathContext());
            $pathInfo = $this->removePrefix($pathInfo, $basePath);

            $copy->setPathInfo($pathInfo);

            $seoPathInfo = $this->getSeoPathInfo($mapping, $config);

            if ($seoPathInfo === null || $seoPathInfo === '') {
                continue;
            }

            $copy->setSeoPathInfo($seoPathInfo);
            $copy->setSalesChannelId($salesChannel->getId());

            yield $copy;
        }
    }

    private function getSeoPathInfo(SeoUrlMapping $mapping, SeoUrlRouteConfig $config): ?string
    {
        try {
            return trim($this->twig->render('template', $mapping->getSeoPathInfoContext()));
        } catch (\Throwable $error) {
            if (!$config->getSkipInvalid()) {
                throw $error;
            }

            return null;
        }
    }

    private function setTwigTemplate(SeoUrlRouteConfig $config, string $template): void
    {
        $template = '{% autoescape \'' . self::ESCAPE_SLUGIFY . "' %}$template{% endautoescape %}";
        $this->twig->setLoader(new ArrayLoader(['template' => $template]));

        try {
            $this->twig->loadTemplate($this->twig->getTemplateClass('template'), 'template');
        } catch (SyntaxError $syntaxError) {
            if (!$config->getSkipInvalid()) {
                throw new InvalidTemplateException('Syntax error: ' . $syntaxError->getMessage());
            }
        }
    }

    private function removePrefix(string $subject, string $prefix): string
    {
        if (!$prefix || mb_strpos($subject, $prefix) !== 0) {
            return $subject;
        }

        return mb_substr($subject, mb_strlen($prefix));
    }

    /**
     * @return list<string>
     */
    private function getAssociations(string $template, EntityDefinition $definition): array
    {
        try {
            $variables = $this->twigVariableParser->parse($template);
        } catch (\Exception $e) {
            $e = new InvalidTemplateException($e->getMessage());

            throw $e;
        }

        $associations = [];
        foreach ($variables as $variable) {
            $fields = EntityDefinitionQueryHelper::getFieldsOfAccessor($definition, $variable, true);

            /** @var Field|null $lastField */
            $lastField = end($fields);

            $runtime = new Runtime();

            if ($lastField && $lastField->getFlag(Runtime::class)) {
                $associations = array_merge($associations, $runtime->getDepends());
            }

            $associations[] = EntityDefinitionQueryHelper::getAssociationPath($variable, $definition);
        }

        return array_filter(array_unique($associations));
    }
}
