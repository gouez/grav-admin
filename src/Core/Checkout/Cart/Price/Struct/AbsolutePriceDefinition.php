<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\Price\Struct;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Rule\Rule;
use Laser\Core\Framework\Struct\Struct;
use Laser\Core\Framework\Util\FloatComparator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * An AbsolutePriceDefinition always return its price value as the final price and adjusts it net worth according to
 * the taxes of other price definitions. This can, for example, be used to create vouchers with a fixed amount.
 */
#[Package('checkout')]
class AbsolutePriceDefinition extends Struct implements PriceDefinitionInterface
{
    final public const TYPE = 'absolute';
    final public const SORTING_PRIORITY = 75;

    /**
     * @var float
     */
    protected $price;

    /**
     * Allows to define a filter rule which line items should be considered for percentage discount/surcharge
     */
    protected ?Rule $filter;

    public function __construct(
        float $price,
        ?Rule $filter = null
    ) {
        $this->price = FloatComparator::cast($price);
        $this->filter = $filter;
    }

    public function getFilter(): ?Rule
    {
        return $this->filter;
    }

    public function getPrice(): float
    {
        return FloatComparator::cast($this->price);
    }

    public function getType(): string
    {
        return self::TYPE;
    }

    public function getPriority(): int
    {
        return self::SORTING_PRIORITY;
    }

    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize();
        $data['type'] = $this->getType();

        return $data;
    }

    public static function getConstraints(): array
    {
        return [
            'price' => [new NotBlank(), new Type('numeric')],
        ];
    }

    public function getApiAlias(): string
    {
        return 'cart_price_absolute';
    }
}
