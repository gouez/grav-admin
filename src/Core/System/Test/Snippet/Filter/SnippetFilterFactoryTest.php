<?php declare(strict_types=1);

namespace Laser\Core\System\Test\Snippet\Filter;

use PHPUnit\Framework\TestCase;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Laser\Core\System\Snippet\Exception\FilterNotFoundException;
use Laser\Core\System\Snippet\Filter\AddedFilter;
use Laser\Core\System\Snippet\Filter\AuthorFilter;
use Laser\Core\System\Snippet\Filter\EditedFilter;
use Laser\Core\System\Snippet\Filter\EmptySnippetFilter;
use Laser\Core\System\Snippet\Filter\NamespaceFilter;
use Laser\Core\System\Snippet\Filter\SnippetFilterFactory;
use Laser\Core\System\Snippet\Filter\TermFilter;
use Laser\Core\System\Snippet\Filter\TranslationKeyFilter;

/**
 * @internal
 */
#[Package('system-settings')]
class SnippetFilterFactoryTest extends TestCase
{
    use IntegrationTestBehaviour;

    /**
     * @dataProvider dataProviderForTestGetFilter
     */
    public function testGetFilter($filterName, $expectedResult, $expectException): void
    {
        $factory = $this->getContainer()->get(SnippetFilterFactory::class);

        if ($expectException) {
            $this->expectException(FilterNotFoundException::class);
        }

        $result = $factory->getFilter($filterName);

        static::assertInstanceOf($expectedResult, $result);
    }

    public static function dataProviderForTestGetFilter(): array
    {
        return [
            ['', null, true],
            ['foo', null, true],
            ['bar', null, true],
            ['author', AuthorFilter::class, false],
            ['edited', EditedFilter::class, false],
            ['empty', EmptySnippetFilter::class, false],
            ['namespace', NamespaceFilter::class, false],
            ['term', TermFilter::class, false],
            ['translationKey', TranslationKeyFilter::class, false],
            ['added', AddedFilter::class, false],
        ];
    }
}
