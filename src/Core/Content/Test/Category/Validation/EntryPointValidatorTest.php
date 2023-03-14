<?php declare(strict_types=1);

namespace Laser\Core\Content\Test\Category\Validation;

use PHPUnit\Framework\TestCase;
use Laser\Core\Content\Category\CategoryDefinition;
use Laser\Core\Content\Category\CategoryEntity;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Write\WriteException;
use Laser\Core\Framework\Test\TestCaseBase\BasicTestDataBehaviour;
use Laser\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;
use Laser\Core\System\SalesChannel\SalesChannelDefinition;
use Laser\Core\Test\TestDefaults;

/**
 * @internal
 */
class EntryPointValidatorTest extends TestCase
{
    use KernelTestBehaviour;
    use BasicTestDataBehaviour;

    /**
     * @var EntityRepository
     */
    private $categoryRepository;

    /**
     * @var EntityRepository
     */
    private $salesChannelRepository;

    protected function setUp(): void
    {
        $this->categoryRepository = $this->getContainer()->get(sprintf('%s.repository', CategoryDefinition::ENTITY_NAME));
        $this->salesChannelRepository = $this->getContainer()->get(sprintf('%s.repository', SalesChannelDefinition::ENTITY_NAME));
    }

    public function testChangeNavigationFail(): void
    {
        $context = Context::createDefaultContext();
        $categoryId = $this->getValidCategoryId();
        $this->salesChannelRepository->update([
            [
                'id' => TestDefaults::SALES_CHANNEL,
                'navigationCategoryId' => $categoryId,
            ],
        ], $context);

        $this->expectException(WriteException::class);
        $this->categoryRepository->update([
            [
                'id' => $categoryId,
                'type' => CategoryDefinition::TYPE_LINK,
            ],
        ], $context);
    }

    public function testChangeServiceFail(): void
    {
        $context = Context::createDefaultContext();
        $categoryId = $this->getValidCategoryId();

        $this->expectException(WriteException::class);
        $this->salesChannelRepository->update([
            [
                'id' => TestDefaults::SALES_CHANNEL,
                'serviceCategory' => [
                    'id' => $categoryId,
                    'type' => CategoryDefinition::TYPE_LINK,
                ],
            ],
        ], $context);
    }

    public function testChangeFooterValid(): void
    {
        $context = Context::createDefaultContext();
        $categoryId = $this->getValidCategoryId();
        $this->salesChannelRepository->update([
            [
                'id' => TestDefaults::SALES_CHANNEL,
                'footerCategory' => [
                    'id' => $categoryId,
                    'type' => CategoryDefinition::TYPE_PAGE,
                ],
            ],
        ], $context);

        /** @var CategoryEntity|null $category */
        $category = $this->categoryRepository->search(new Criteria([$categoryId]), $context)->first();
        static::assertNotNull($category);
        static::assertSame(CategoryDefinition::TYPE_PAGE, $category->getType());
    }
}
