<?php declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer\Pricing;

use Laser\Core\Framework\DataAbstractionLayer\Entity;
use Laser\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Laser\Core\Framework\Log\Package;

#[Package('core')]
class PriceRuleEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $ruleId;

    /**
     * @var PriceCollection
     */
    protected $price;

    public function getRuleId(): string
    {
        return $this->ruleId;
    }

    public function setRuleId(string $ruleId): void
    {
        $this->ruleId = $ruleId;
    }

    public function getPrice(): PriceCollection
    {
        return $this->price;
    }

    public function setPrice(PriceCollection $price): void
    {
        $this->price = $price;
    }
}
