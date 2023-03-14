<?php declare(strict_types=1);

namespace Laser\Core\System\Currency\SalesChannel;

use Laser\Core\Framework\Log\Package;
use Laser\Core\System\Currency\CurrencyCollection;
use Laser\Core\System\SalesChannel\StoreApiResponse;

#[Package('inventory')]
class CurrencyRouteResponse extends StoreApiResponse
{
    /**
     * @var CurrencyCollection
     */
    protected $object;

    public function __construct(CurrencyCollection $currencies)
    {
        parent::__construct($currencies);
    }

    public function getCurrencies(): CurrencyCollection
    {
        return $this->object;
    }
}
