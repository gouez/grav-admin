<?php declare(strict_types=1);

namespace Laser\Core\Test\Integration\Traits;

use Doctrine\DBAL\Connection;
use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Checkout\Cart\LineItem\LineItem;
use Laser\Core\Checkout\Cart\LineItemFactoryHandler\ProductLineItemFactory;
use Laser\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Laser\Core\Checkout\Cart\SalesChannel\CartService;
use Laser\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemEntity;
use Laser\Core\Checkout\Test\Customer\CustomerBuilder;
use Laser\Core\Content\Flow\Events\FlowSendMailActionEvent;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\AndFilter;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Laser\Core\Framework\Test\IdsCollection;
use Laser\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\Framework\Validation\DataBag\RequestDataBag;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextFactory;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextService;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Laser\Core\Test\Integration\Helper\MailEventListener;
use Laser\Core\Test\TestDefaults;

/**
 * @internal
 */
trait TestShortHands
{
    use KernelTestBehaviour;

    /**
     * @param array<string, mixed> $options
     */
    protected function getContext(?string $token = null, array $options = []): SalesChannelContext
    {
        $token ??= Uuid::randomHex();

        return $this->getContainer()->get(SalesChannelContextFactory::class)
            ->create($token, TestDefaults::SALES_CHANNEL, $options);
    }

    protected function addProductToCart(string $id, SalesChannelContext $context): Cart
    {
        $product = $this->getContainer()->get(ProductLineItemFactory::class)
            ->create(['id' => $id, 'referencedId' => $id], $context);

        $cart = $this->getContainer()->get(CartService::class)
            ->getCart($context->getToken(), $context);

        return $this->getContainer()->get(CartService::class)
            ->add($cart, $product, $context);
    }

    protected function order(Cart $cart, SalesChannelContext $context, ?RequestDataBag $data = null): string
    {
        return $this->getContainer()->get(CartService::class)
            ->order($cart, $context, $data ?? new RequestDataBag());
    }

    protected function assertProductInOrder(string $orderId, string $productId): OrderLineItemEntity
    {
        $criteria = new Criteria();
        $criteria->setLimit(1);

        $criteria->addFilter(new AndFilter([
            new EqualsFilter('referencedId', $productId),
            new EqualsFilter('type', LineItem::PRODUCT_LINE_ITEM_TYPE),
            new EqualsFilter('orderId', $orderId),
        ]));

        $exists = $this->getContainer()->get('order_line_item.repository')
            ->search($criteria, Context::createDefaultContext());

        static::assertCount(1, $exists);

        $item = $exists->first();

        static::assertInstanceOf(OrderLineItemEntity::class, $item);

        return $item;
    }

    protected function assertLineItemTotalPrice(Cart $cart, string $id, float $price): void
    {
        $item = $cart->get($id);

        static::assertInstanceOf(LineItem::class, $item, \sprintf('Can not find line item with id %s', $id));

        static::assertInstanceOf(CalculatedPrice::class, $item->getPrice(), \sprintf('Line item with id %s has no price', $id));

        static::assertEquals($price, $item->getPrice()->getTotalPrice(), \sprintf('Line item with id %s has wrong total price', $id));
    }

    protected function assertLineItemUnitPrice(Cart $cart, string $id, float $price): void
    {
        $item = $cart->get($id);

        static::assertInstanceOf(LineItem::class, $item, \sprintf('Can not find line item with id %s', $id));

        static::assertInstanceOf(CalculatedPrice::class, $item->getPrice(), \sprintf('Line item with id %s has no price', $id));

        static::assertEquals($price, $item->getPrice()->getUnitPrice(), \sprintf('Line item with id %s has wrong unit price', $id));
    }

    protected function assertLineItemInCart(Cart $cart, string $id): void
    {
        $item = $cart->get($id);

        static::assertInstanceOf(LineItem::class, $item, \sprintf('Can not find line item with id %s', $id));
    }

    protected function login(SalesChannelContext $context, ?string $customerId = null): SalesChannelContext
    {
        if ($customerId === null) {
            $customer = new CustomerBuilder(
                new IdsCollection(),
                Uuid::randomHex(),
                $context->getSalesChannelId()
            );

            $this->getContainer()->get('customer.repository')->create(
                [$customer->build()],
                Context::createDefaultContext()
            );

            $customerId = $customer->id;
        }

        return $this->getContext($context->getToken(), [
            SalesChannelContextService::CUSTOMER_ID => $customerId,
        ]);
    }

    protected function assertMailSent(MailEventListener $listener, string $type): void
    {
        static::assertTrue($listener->sent($type), \sprintf('Mail with type %s was not sent', $type));
    }

    /**
     * @return mixed
     */
    protected function mailListener(\Closure $closure)
    {
        $mapping = $this->getContainer()->get(Connection::class)
            ->fetchAllKeyValue('SELECT LOWER(HEX(id)), technical_name FROM mail_template_type');

        $listener = new MailEventListener($mapping);

        $dispatcher = $this->getContainer()->get('event_dispatcher');

        $dispatcher->addListener(FlowSendMailActionEvent::class, $listener);

        $result = $closure($listener);

        $dispatcher->removeListener(FlowSendMailActionEvent::class, $listener);

        return $result;
    }

    private function assertStock(string $productId, int $stock, int $available): void
    {
        /** @var array{stock: int, available_stock:int} $stocks */
        $stocks = $this->getContainer()->get(Connection::class)->fetchAssociative(
            'SELECT stock, available_stock FROM product WHERE id = :id',
            ['id' => Uuid::fromHexToBytes($productId)]
        );

        static::assertNotEmpty($stocks, sprintf('Product with id %s not found', $productId));

        static::assertEquals($stock, (int) $stocks['stock'], sprintf('Product with id %s has wrong stock', $productId));

        static::assertEquals($available, $stocks['available_stock'], sprintf('Product with id %s has wrong available stock', $productId));
    }
}
