<?php declare(strict_types=1);

namespace Laser\Core\Framework\App\ScheduledTask;

use Laser\Core\Defaults;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @internal
 */
#[AsMessageHandler(handles: DeleteCascadeAppsTask::class)]
#[Package('core')]

final class DeleteCascadeAppsHandler extends ScheduledTaskHandler
{
    private const HARD_DELETE_AFTER_DAYS = 1;

    /**
     * @internal
     */
    public function __construct(
        EntityRepository $scheduledTaskRepository,
        private readonly EntityRepository $aclRoleRepository,
        private readonly EntityRepository $integrationRepository
    ) {
        parent::__construct($scheduledTaskRepository);
    }

    public function run(): void
    {
        $context = Context::createDefaultContext();
        $timeExpired = (new \DateTimeImmutable())->modify(sprintf('-%s day', self::HARD_DELETE_AFTER_DAYS))->format(Defaults::STORAGE_DATE_TIME_FORMAT);

        $criteria = new Criteria();
        $criteria->addFilter(new RangeFilter('deletedAt', [
            RangeFilter::LTE => $timeExpired,
        ]));

        $this->deleteIds($this->aclRoleRepository, $criteria, $context);
        $this->deleteIds($this->integrationRepository, $criteria, $context);
    }

    private function deleteIds(EntityRepository $repository, Criteria $criteria, Context $context): void
    {
        $data = $repository->searchIds($criteria, $context)->getData();

        if (empty($data)) {
            return;
        }

        $deleteIds = array_values($data);

        $repository->delete($deleteIds, $context);
    }
}
