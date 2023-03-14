<?php declare(strict_types=1);

namespace Laser\Core\Content\Media\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;

#[Package('content')]
class StrategyNotFoundException extends LaserHttpException
{
    public function __construct(string $strategyName)
    {
        parent::__construct(
            'No Strategy with name "{{ strategyName }}" found.',
            ['strategyName' => $strategyName]
        );
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__MEDIA_STRATEGY_NOT_FOUND';
    }
}
