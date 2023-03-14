<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\TestCaseBase;

use PHPUnit\Framework\TestCase;
use Laser\Core\Framework\Event\EventData\MailRecipientStruct;
use Laser\Core\Framework\Event\MailAware;
use Laser\Core\Framework\Event\LaserEvent;
use Laser\Core\System\SalesChannel\SalesChannelContext;

trait MailTemplateTestBehaviour
{
    use EventDispatcherBehaviour;

    /**
     * @param class-string<object> $expectedClass
     */
    public static function assertMailEvent(
        string $expectedClass,
        LaserEvent $event,
        SalesChannelContext $salesChannelContext
    ): void {
        TestCase::assertInstanceOf($expectedClass, $event);
        TestCase::assertSame($salesChannelContext->getContext(), $event->getContext());
    }

    public static function assertMailRecipientStructEvent(MailRecipientStruct $expectedStruct, MailAware $event): void
    {
        TestCase::assertSame($expectedStruct->getRecipients(), $event->getMailStruct()->getRecipients());
    }

    protected function catchEvent(string $eventName, ?object &$eventResult): void
    {
        $eventDispatcher = $this->getContainer()->get('event_dispatcher');
        $this->addEventListener($eventDispatcher, $eventName, static function ($event) use (&$eventResult): void {
            $eventResult = $event;
        });
    }
}
