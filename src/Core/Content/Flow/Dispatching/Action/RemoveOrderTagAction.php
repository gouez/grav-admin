<?php declare(strict_types=1);

namespace Laser\Core\Content\Flow\Dispatching\Action;

use Laser\Core\Content\Flow\Dispatching\DelayableAction;
use Laser\Core\Content\Flow\Dispatching\StorableFlow;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\Event\OrderAware;
use Laser\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('business-ops')]
class RemoveOrderTagAction extends FlowAction implements DelayableAction
{
    /**
     * @internal
     */
    public function __construct(private readonly EntityRepository $orderTagRepository)
    {
    }

    public static function getName(): string
    {
        return 'action.remove.order.tag';
    }

    /**
     * @return array<int, string>
     */
    public function requirements(): array
    {
        return [OrderAware::class];
    }

    public function handleFlow(StorableFlow $flow): void
    {
        if (!$flow->hasStore(OrderAware::ORDER_ID)) {
            return;
        }

        $this->update($flow->getContext(), $flow->getConfig(), $flow->getStore(OrderAware::ORDER_ID));
    }

    /**
     * @param array<string, mixed> $config
     */
    private function update(Context $context, array $config, string $orderId): void
    {
        if (!\array_key_exists('tagIds', $config)) {
            return;
        }

        $tagIds = array_keys($config['tagIds']);

        if (empty($tagIds)) {
            return;
        }

        $tags = array_map(static fn ($tagId) => [
            'orderId' => $orderId,
            'tagId' => $tagId,
        ], $tagIds);

        $this->orderTagRepository->delete($tags, $context);
    }
}
