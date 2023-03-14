<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\DependencyInjection\fixtures;

/**
 * @internal
 */
final class TestBusinessEvents
{
    /**
     * @Event("Laser\Core\Framework\Test\DependencyInjection\fixtures\TestEvent")
     */
    public const TEST_EVENT = TestEvent::EVENT_NAME;

    private function __construct()
    {
    }
}
