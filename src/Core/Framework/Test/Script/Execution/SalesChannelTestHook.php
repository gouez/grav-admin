<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\Script\Execution;

use Laser\Core\Framework\Script\Execution\Awareness\SalesChannelContextAware;
use Laser\Core\Framework\Script\Execution\Awareness\SalesChannelContextAwareTrait;
use Laser\Core\Framework\Script\Execution\Hook;
use Laser\Core\System\SalesChannel\SalesChannelContext;

/**
 * @internal
 */
class SalesChannelTestHook extends Hook implements SalesChannelContextAware
{
    use SalesChannelContextAwareTrait;

    private static array $serviceIds;

    /**
     * @param array<string> $serviceIds
     */
    public function __construct(
        private readonly string $name,
        SalesChannelContext $context,
        array $data = [],
        array $serviceIds = []
    ) {
        parent::__construct($context->getContext());
        $this->salesChannelContext = $context;
        self::$serviceIds = $serviceIds;

        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public static function getServiceIds(): array
    {
        return self::$serviceIds;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
