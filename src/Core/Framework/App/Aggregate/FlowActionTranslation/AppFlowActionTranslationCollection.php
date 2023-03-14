<?php declare(strict_types=1);

namespace Laser\Core\Framework\App\Aggregate\FlowActionTranslation;

use Laser\Core\Framework\DataAbstractionLayer\EntityCollection;
use Laser\Core\Framework\Log\Package;

/**
 * @extends EntityCollection<AppFlowActionTranslationEntity>
 */
#[Package('core')]
class AppFlowActionTranslationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return AppFlowActionTranslationEntity::class;
    }
}
