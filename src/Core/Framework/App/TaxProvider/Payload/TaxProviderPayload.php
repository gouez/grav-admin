<?php declare(strict_types=1);

namespace Laser\Core\Framework\App\TaxProvider\Payload;

use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Framework\App\Payment\Payload\Struct\Source;
use Laser\Core\Framework\App\Payment\Payload\Struct\SourcedPayloadInterface;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\CloneTrait;
use Laser\Core\Framework\Struct\JsonSerializableTrait;
use Laser\Core\System\SalesChannel\SalesChannelContext;

/**
 * @internal only for use by the app-system
 */
#[Package('checkout')]
class TaxProviderPayload implements SourcedPayloadInterface
{
    use CloneTrait;
    use JsonSerializableTrait;

    private Source $source;

    public function __construct(
        private readonly Cart $cart,
        private readonly SalesChannelContext $context
    ) {
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }

    public function getContext(): SalesChannelContext
    {
        return $this->context;
    }

    public function setSource(Source $source): void
    {
        $this->source = $source;
    }
}
