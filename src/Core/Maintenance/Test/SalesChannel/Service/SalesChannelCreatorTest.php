<?php declare(strict_types=1);

namespace Laser\Core\Maintenance\Test\SalesChannel\Service;

use PHPUnit\Framework\TestCase;
use Laser\Core\Defaults;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\Maintenance\SalesChannel\Service\SalesChannelCreator;
use Laser\Core\System\SalesChannel\SalesChannelEntity;

/**
 * @internal
 */
#[Package('core')]
class SalesChannelCreatorTest extends TestCase
{
    use IntegrationTestBehaviour;

    private SalesChannelCreator $salesChannelCreator;

    private EntityRepository $salesChannelRepository;

    public function setUp(): void
    {
        $this->salesChannelCreator = $this->getContainer()->get(SalesChannelCreator::class);
        $this->salesChannelRepository = $this->getContainer()->get('sales_channel.repository');
    }

    public function testCreateSalesChannel(): void
    {
        $id = Uuid::randomHex();
        $this->salesChannelCreator->createSalesChannel($id, 'test', Defaults::SALES_CHANNEL_TYPE_API);

        /** @var SalesChannelEntity $salesChannel */
        $salesChannel = $this->salesChannelRepository->search(new Criteria([$id]), Context::createDefaultContext())->first();

        static::assertNotNull($salesChannel);
        static::assertEquals('test', $salesChannel->getName());
        static::assertEquals(Defaults::SALES_CHANNEL_TYPE_API, $salesChannel->getTypeId());
    }
}
