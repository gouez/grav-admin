<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\TestCaseBase;

use Laser\Core\System\SalesChannel\SalesChannelContext;
use Laser\Core\System\Tax\Aggregate\TaxRule\TaxRuleCollection;
use Laser\Core\System\Tax\TaxEntity;

trait TaxAddToSalesChannelTestBehaviour
{
    /**
     * @param array<mixed> $taxData
     */
    protected function addTaxDataToSalesChannel(SalesChannelContext $salesChannelContext, array $taxData): void
    {
        $tax = (new TaxEntity())->assign($taxData);
        $this->addTaxEntityToSalesChannel($salesChannelContext, $tax);
    }

    protected function addTaxEntityToSalesChannel(SalesChannelContext $salesChannelContext, TaxEntity $taxEntity): void
    {
        if ($taxEntity->getRules() === null) {
            $taxEntity->setRules(new TaxRuleCollection());
        }
        $salesChannelContext->getTaxRules()->add($taxEntity);
    }
}
