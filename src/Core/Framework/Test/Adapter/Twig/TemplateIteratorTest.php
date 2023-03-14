<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\Adapter\Twig;

use PHPUnit\Framework\TestCase;
use Laser\Core\Framework\Adapter\Twig\TemplateIterator;
use Laser\Core\Framework\Bundle;
use Laser\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;

/**
 * @internal
 */
class TemplateIteratorTest extends TestCase
{
    use KernelTestBehaviour;

    /**
     * @var TemplateIterator
     */
    private $iterator;

    protected function setUp(): void
    {
        $this->iterator = $this->getContainer()->get(TemplateIterator::class);
    }

    public function testIteratorDoesNotFullPath(): void
    {
        $templateList = iterator_to_array($this->iterator, false);
        $bundles = $this->getContainer()->getParameter('kernel.bundles');
        $laserBundles = [];

        foreach ($bundles as $bundleName => $bundleClass) {
            if (isset(class_parents($bundleClass)[Bundle::class])) {
                $laserBundles[] = '@' . $bundleName . '/';
            }
        }

        foreach ($laserBundles as $laserBundle) {
            foreach ($templateList as $template) {
                static::assertStringNotContainsStringIgnoringCase($laserBundle, $template);
            }
        }
    }
}
