<?php declare(strict_types=1);

namespace Laser\Core\System\Test\SalesChannel\Subscriber;

use PHPUnit\Framework\TestCase;
use Laser\Core\Defaults;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Write\WriteException;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Test\TestCaseBase\SalesChannelFunctionalTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\System\SalesChannel\Exception\DefaultSalesChannelTypeCannotBeDeleted;

/**
 * @internal
 */
#[Package('sales-channel')]
class SalesChannelTypeValidatorTest extends TestCase
{
    use SalesChannelFunctionalTestBehaviour;

    /**
     * @dataProvider listAvailable
     */
    public function testCannotBeDeleted(string $id): void
    {
        $repo = $this->getContainer()->get('sales_channel_type.repository');

        try {
            $repo->delete([
                [
                    'id' => $id,
                ],
            ], Context::createDefaultContext());
        } catch (WriteException $e) {
            static::assertInstanceOf(DefaultSalesChannelTypeCannotBeDeleted::class, $e->getExceptions()[0]);

            return;
        }

        static::fail('Exception DefaultSalesChannelTypeCannotBeDeleted did not fired');
    }

    public function testDeleteOtherItem(): void
    {
        $repo = $this->getContainer()->get('sales_channel_type.repository');
        $id = Uuid::randomHex();
        $context = Context::createDefaultContext();

        $repo->create([
            [
                'id' => $id,
                'name' => 'test',
            ],
        ], $context);

        $repo->delete([
            [
                'id' => $id,
            ],
        ], $context);

        static::assertNull($repo->searchIds(new Criteria([$id]), $context)->firstId());
    }

    public function testDeleteSalesChannel(): void
    {
        $id = $this->createSalesChannel()['id'];

        $repo = $this->getContainer()->get('sales_channel.repository');
        $repo->delete([
            [
                'id' => $id,
            ],
        ], Context::createDefaultContext());
    }

    public static function listAvailable(): array
    {
        return [
            [Defaults::SALES_CHANNEL_TYPE_API],
            [Defaults::SALES_CHANNEL_TYPE_STOREFRONT],
            [Defaults::SALES_CHANNEL_TYPE_PRODUCT_COMPARISON],
        ];
    }
}
