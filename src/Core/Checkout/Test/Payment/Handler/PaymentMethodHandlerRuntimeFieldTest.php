<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Payment\Handler;

use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\Payment\Cart\PaymentHandler\AsynchronousPaymentHandlerInterface;
use Laser\Core\Checkout\Payment\Cart\PaymentHandler\PreparedPaymentHandlerInterface;
use Laser\Core\Checkout\Payment\Cart\PaymentHandler\SynchronousPaymentHandlerInterface;
use Laser\Core\Checkout\Payment\DataAbstractionLayer\PaymentHandlerIdentifierSubscriber;
use Laser\Core\Checkout\Payment\PaymentMethodEntity;
use Laser\Core\Checkout\Test\Payment\Handler\V630\MultipleTestPaymentHandler;
use Laser\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent;
use Laser\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('checkout')]
class PaymentMethodHandlerRuntimeFieldTest extends TestCase
{
    public function testSynchronousRuntimeField(): void
    {
        $event = $this->createMock(EntityLoadedEvent::class);
        $event
            ->method('getEntities')
            ->willReturn($this->getPaymentMethodEntity(
                $this->createMock(SynchronousPaymentHandlerInterface::class)::class
            ));

        (new PaymentHandlerIdentifierSubscriber())->formatHandlerIdentifier($event);

        static::assertCount(1, $event->getEntities());

        /** @var PaymentMethodEntity $paymentMethod */
        $paymentMethod = $event->getEntities()[0];

        static::assertTrue($paymentMethod->isSynchronous());
        static::assertFalse($paymentMethod->isAsynchronous());
        static::assertFalse($paymentMethod->isPrepared());
    }

    public function testAsynchronousRuntimeField(): void
    {
        $event = $this->createMock(EntityLoadedEvent::class);
        $event
            ->method('getEntities')
            ->willReturn($this->getPaymentMethodEntity(
                $this->createMock(AsynchronousPaymentHandlerInterface::class)::class
            ));

        (new PaymentHandlerIdentifierSubscriber())->formatHandlerIdentifier($event);

        static::assertCount(1, $event->getEntities());

        /** @var PaymentMethodEntity $paymentMethod */
        $paymentMethod = $event->getEntities()[0];

        static::assertFalse($paymentMethod->isSynchronous());
        static::assertTrue($paymentMethod->isAsynchronous());
        static::assertFalse($paymentMethod->isPrepared());
    }

    public function testPreparedRuntimeField(): void
    {
        $event = $this->createMock(EntityLoadedEvent::class);
        $event
            ->method('getEntities')
            ->willReturn($this->getPaymentMethodEntity(
                $this->createMock(PreparedPaymentHandlerInterface::class)::class
            ));

        (new PaymentHandlerIdentifierSubscriber())->formatHandlerIdentifier($event);

        static::assertCount(1, $event->getEntities());

        /** @var PaymentMethodEntity $paymentMethod */
        $paymentMethod = $event->getEntities()[0];

        static::assertFalse($paymentMethod->isSynchronous());
        static::assertFalse($paymentMethod->isAsynchronous());
        static::assertTrue($paymentMethod->isPrepared());
    }

    public function testMultipleRuntimeFieldsAtOnce(): void
    {
        $event = $this->createMock(EntityLoadedEvent::class);
        $event
            ->method('getEntities')
            ->willReturn($this->getPaymentMethodEntity(
                $this->createMock(MultipleTestPaymentHandler::class)::class
            ));

        (new PaymentHandlerIdentifierSubscriber())->formatHandlerIdentifier($event);

        static::assertCount(1, $event->getEntities());

        /** @var PaymentMethodEntity $paymentMethod */
        $paymentMethod = $event->getEntities()[0];

        static::assertTrue($paymentMethod->isSynchronous());
        static::assertFalse($paymentMethod->isAsynchronous());
        static::assertTrue($paymentMethod->isPrepared());
    }

    /**
     * @return PaymentMethodEntity[]
     */
    private function getPaymentMethodEntity(string $handlerIdentifier): array
    {
        return [(new PaymentMethodEntity())->assign(['handlerIdentifier' => $handlerIdentifier])];
    }
}
