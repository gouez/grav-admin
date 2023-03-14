<?php declare(strict_types=1);

namespace Laser\Core\Content\Test\Flow;

use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\Order\OrderEntity;
use Laser\Core\Content\Flow\Dispatching\Action\AddOrderAffiliateAndCampaignCodeAction;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Test\TestDataCollection;
use Laser\Core\Framework\Uuid\Uuid;

/**
 * @internal
 */
#[Package('business-ops')]
class AddOrderAffiliateAndCampaignCodeActionTest extends TestCase
{
    use OrderActionTrait;

    private EntityRepository $flowRepository;

    protected function setUp(): void
    {
        $this->flowRepository = $this->getContainer()->get('flow.repository');

        $this->customerRepository = $this->getContainer()->get('customer.repository');

        $this->ids = new TestDataCollection();

        $this->browser = $this->createCustomSalesChannelBrowser([
            'id' => $this->ids->create('sales-channel'),
        ]);

        $this->browser->setServerParameter('HTTP_SW_CONTEXT_TOKEN', $this->ids->create('token'));
    }

    /**
     * @param array<string, mixed> $existedData
     * @param array<string, mixed> $updateData
     * @param array<string, mixed> $expectData
     *
     * @dataProvider createDataProvider
     */
    public function testAddAffiliateAndCampaignCodeForOrder(array $existedData, array $updateData, array $expectData): void
    {
        $this->createCustomerAndLogin();
        $this->createOrder($this->ids->get('customer'), $existedData);

        $sequenceId = Uuid::randomHex();
        $this->flowRepository->create([[
            'name' => 'Cancel order',
            'eventName' => 'state_enter.order.state.cancelled',
            'priority' => 1,
            'active' => true,
            'sequences' => [
                [
                    'id' => $sequenceId,
                    'parentId' => null,
                    'ruleId' => null,
                    'actionName' => AddOrderAffiliateAndCampaignCodeAction::getName(),
                    'position' => 1,
                    'config' => $updateData,
                ],
            ],
        ]], Context::createDefaultContext());

        $this->cancelOrder();

        /** @var OrderEntity $order */
        $order = $this->getContainer()->get('order.repository')->search(new Criteria([$this->ids->get('order')]), Context::createDefaultContext())->first();

        static::assertEquals($order->getAffiliateCode(), $expectData['affiliateCode']);
        static::assertEquals($order->getCampaignCode(), $expectData['campaignCode']);
    }

    /**
     * @return array<int, mixed>
     */
    public static function createDataProvider(): array
    {
        return [
            // existed data / update data / expect data
            [
                [],
                [
                    'affiliateCode' => ['value' => '11111', 'upsert' => false],
                    'campaignCode' => ['value' => '22222', 'upsert' => false],
                ],
                ['affiliateCode' => '11111', 'campaignCode' => '22222'],
            ],
            [
                ['affiliateCode' => '11111', 'campaignCode' => '22222'],
                [
                    'affiliateCode' => ['value' => '33333', 'upsert' => false],
                    'campaignCode' => ['value' => '33333', 'upsert' => false],
                ],
                ['affiliateCode' => '11111', 'campaignCode' => '22222'],
            ],
            [
                ['affiliateCode' => '11111', 'campaignCode' => '22222'],
                [
                    'affiliateCode' => ['value' => '33333', 'upsert' => false],
                    'campaignCode' => ['value' => '33333', 'upsert' => true],
                ],
                ['affiliateCode' => '11111', 'campaignCode' => '33333'],
            ],
            [
                ['affiliateCode' => '11111', 'campaignCode' => '22222'],
                [
                    'affiliateCode' => ['value' => '33333', 'upsert' => true],
                    'campaignCode' => ['value' => '33333', 'upsert' => true],
                ],
                ['affiliateCode' => '33333', 'campaignCode' => '33333'],
            ],
        ];
    }
}
