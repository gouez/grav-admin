<?php declare(strict_types=1);

namespace Laser\Core\Framework\DependencyInjection\CompilerPass;

use Doctrine\Common\Annotations\DocParser;
use Laser\Core\Framework\Event\Annotation\Event;
use Laser\Core\Framework\Event\BusinessEventRegistry;
use Laser\Core\Framework\Event\BusinessEvents;
use Laser\Core\Framework\Event\FlowEventAware;
use Laser\Core\Framework\Feature;
use Laser\Core\Framework\Log\Package;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Contracts\EventDispatcher\Event as SymfonyBaseEvent;

#[Package('core')]
class ActionEventCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $classes = [];
        foreach ($this->getEventClasses() as $eventClass) {
            if (!is_subclass_of($eventClass, FlowEventAware::class)) {
                continue;
            }

            $classes[] = $eventClass;
        }

        $definition = $container->getDefinition(BusinessEventRegistry::class);
        $definition->addMethodCall('addClasses', [$classes]);
    }

    /**
     * @return \ReflectionClass<BusinessEvents>
     */
    protected function getReflectionClass(): \ReflectionClass
    {
        return new \ReflectionClass(BusinessEvents::class);
    }

    /**
     * @return array<string, class-string<SymfonyBaseEvent>>
     */
    private function getEventClasses(): array
    {
        $reflectionClass = $this->getReflectionClass();
        $docParser = $this->getDocParser();

        $eventClasses = [];
        foreach ($reflectionClass->getReflectionConstants() as $constant) {
            $docComment = $constant->getDocComment();
            if (!\is_string($docComment)) {
                continue;
            }

            foreach ($docParser->parse($docComment) as $annotation) {
                if ($annotation instanceof Event) {
                    $deprecationVersion = $annotation->getDeprecationVersion();

                    if ($deprecationVersion && Feature::isActive($deprecationVersion)) {
                        continue;
                    }

                    $eventClasses[(string) $constant->getValue()] = $annotation->getEventClass();
                }
            }
        }

        return $eventClasses;
    }

    private function getDocParser(): DocParser
    {
        $docParser = new DocParser();
        $docParser->setImports([
            'event' => Event::class,
        ]);
        $docParser->setIgnoreNotImportedAnnotations(true);

        return $docParser;
    }
}
