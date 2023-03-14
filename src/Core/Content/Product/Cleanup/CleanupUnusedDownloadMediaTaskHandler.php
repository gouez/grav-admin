<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\Cleanup;

use Laser\Core\Content\Media\DeleteNotUsedMediaService;
use Laser\Core\Content\Product\Aggregate\ProductDownload\ProductDownloadDefinition;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Laser\Core\Framework\Struct\ArrayStruct;

/**
 * @internal
 */
#[Package('inventory')]
final class CleanupUnusedDownloadMediaTaskHandler extends ScheduledTaskHandler
{
    public function __construct(
        EntityRepository $repository,
        private readonly DeleteNotUsedMediaService $deleteMediaService
    ) {
        parent::__construct($repository);
    }

    /**
     * @return string[]
     */
    public static function getHandledMessages(): iterable
    {
        return [CleanupUnusedDownloadMediaTask::class];
    }

    public function run(): void
    {
        $context = Context::createDefaultContext();

        $context->addExtension(
            DeleteNotUsedMediaService::RESTRICT_DEFAULT_FOLDER_ENTITIES_EXTENSION,
            new ArrayStruct([ProductDownloadDefinition::ENTITY_NAME])
        );

        $this->deleteMediaService->deleteNotUsedMedia($context);
    }
}
