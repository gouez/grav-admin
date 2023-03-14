<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Cart\Promotion\Helpers;

use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\System\SalesChannel\Context\AbstractSalesChannelContextFactory;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Laser\Core\Test\TestDefaults;

/**
 * @internal
 */
#[Package('checkout')]
class PromotionFixtureBuilder
{
    private readonly SalesChannelContext $context;

    private ?string $code = null;

    private array $dataSetGroups;

    private array $dataDiscounts;

    public function __construct(
        private readonly string $promotionId,
        AbstractSalesChannelContextFactory $salesChannelContextFactory,
        private readonly EntityRepository $promotionRepository,
        private readonly EntityRepository $promotionSetgroupRepository,
        private readonly EntityRepository $promotionDiscountRepository
    ) {
        $this->dataSetGroups = [];
        $this->dataDiscounts = [];

        $this->context = $salesChannelContextFactory->create(Uuid::randomHex(), TestDefaults::SALES_CHANNEL);
    }

    public function setCode(string $code): PromotionFixtureBuilder
    {
        $this->code = $code;

        return $this;
    }

    public function addDiscount(
        string $scope,
        string $type,
        float $value,
        bool $considerAdvancedRules,
        ?float $maxValue
    ): PromotionFixtureBuilder {
        $data = [
            'id' => Uuid::randomHex(),
            'promotionId' => $this->promotionId,
            'scope' => $scope,
            'type' => $type,
            'value' => $value,
            'considerAdvancedRules' => $considerAdvancedRules,
        ];

        if ($maxValue !== null) {
            $data['maxValue'] = $maxValue;
        }

        $this->dataDiscounts[] = $data;

        return $this;
    }

    public function addSetGroup(string $packagerKey, float $value, string $sorterKey): PromotionFixtureBuilder
    {
        $this->dataSetGroups[] = [
            'id' => Uuid::randomHex(),
            'promotionId' => $this->promotionId,
            'packagerKey' => $packagerKey,
            'sorterKey' => $sorterKey,
            'value' => $value,
        ];

        return $this;
    }

    /**
     * Builds our configured promotion and saves all related
     * entities and objects in the database.
     */
    public function buildPromotion(): void
    {
        $data = [
            'id' => $this->promotionId,
            'name' => 'Black Friday',
            'active' => true,
            'useCodes' => false,
            'useSetGroups' => false,
            'salesChannels' => [
                ['salesChannelId' => TestDefaults::SALES_CHANNEL, 'priority' => 1],
            ],
        ];

        if ($this->code !== null) {
            $data['code'] = $this->code;
            $data['useCodes'] = true;
        }

        if (\count($this->dataSetGroups) > 0) {
            $data['useSetGroups'] = true;
        }

        // save the promotion
        $this->promotionRepository->create([$data], $this->context->getContext());

        // save our defined set groups
        if (\count($this->dataSetGroups) > 0) {
            $this->promotionSetgroupRepository->create($this->dataSetGroups, $this->context->getContext());
        }

        // save our added discounts
        if (\count($this->dataDiscounts) > 0) {
            $this->promotionDiscountRepository->create($this->dataDiscounts, $this->context->getContext());
        }
    }
}
