<?php declare(strict_types=1);

namespace Laser\Core\System\Test\NumberRange\Aggregate\NumberRangeSalesChannel;

use PHPUnit\Framework\TestCase;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\System\NumberRange\Aggregate\NumberRangeSalesChannel\NumberRangeSalesChannelCollection;
use Laser\Core\System\NumberRange\Aggregate\NumberRangeSalesChannel\NumberRangeSalesChannelEntity;
use Laser\Core\Test\TestDefaults;

/**
 * @internal
 */
class NumberRangeSalesChannelDefinitionTest extends TestCase
{
    use IntegrationTestBehaviour;

    private EntityRepository $numberRangeRepository;

    private EntityRepository $salesChannelRepository;

    public function setUp(): void
    {
        $this->numberRangeRepository = $this->getContainer()->get('number_range.repository');
        $this->salesChannelRepository = $this->getContainer()->get('sales_channel.repository');
    }

    public function testNumberRangeSalesChannelCollectionFromNumberRange(): void
    {
        $numberRangeId = $this->createNumberRange();

        $criteria = new Criteria([$numberRangeId]);
        $criteria->addAssociation('numberRangeSalesChannels');

        $numberRange = $this->numberRangeRepository->search($criteria, Context::createDefaultContext())->first();

        $this->assertNumberRangeSalesChannel($numberRangeId, $numberRange->getNumberRangeSalesChannels());
    }

    public function testNumberRangeSalesChannelCollectionFromSalesChannel(): void
    {
        $numberRangeId = $this->createNumberRange();

        $criteria = new Criteria([TestDefaults::SALES_CHANNEL]);
        $criteria->addAssociation('numberRangeSalesChannels');

        $salesChannel = $this->salesChannelRepository->search($criteria, Context::createDefaultContext())->first();

        $this->assertNumberRangeSalesChannel($numberRangeId, $salesChannel->getNumberRangeSalesChannels());
    }

    private function createNumberRange(): string
    {
        $numberRangeId = Uuid::randomHex();

        $this->numberRangeRepository->create([[
            'id' => $numberRangeId,
            'name' => 'numberRange',
            'pattern' => '{n}',
            'start' => 0,
            'global' => false,
            'type' => [
                'id' => $numberRangeId,
                'typeName' => 'number range type',
                'technicalName' => 'number_range_type',
                'global' => false,
            ],
            'numberRangeSalesChannels' => [
                [
                    'numberRangeId' => $numberRangeId,
                    'salesChannelId' => TestDefaults::SALES_CHANNEL,
                    'numberRangeTypeId' => $numberRangeId,
                ],
            ],
        ]], Context::createDefaultContext());

        return $numberRangeId;
    }

    private function assertNumberRangeSalesChannel(
        string $numberRangeId,
        ?NumberRangeSalesChannelCollection $getNumberRangeSalesChannels
    ): void {
        static::assertInstanceOf(NumberRangeSalesChannelCollection::class, $getNumberRangeSalesChannels);

        $numberRangeSalesChannel = $getNumberRangeSalesChannels->first();

        static::assertInstanceOf(NumberRangeSalesChannelEntity::class, $numberRangeSalesChannel);
        static::assertEquals($numberRangeId, $numberRangeSalesChannel->getNumberRangeId());
        static::assertEquals(TestDefaults::SALES_CHANNEL, $numberRangeSalesChannel->getSalesChannelId());
        static::assertEquals($numberRangeId, $numberRangeSalesChannel->getNumberRangeTypeId());
    }
}
