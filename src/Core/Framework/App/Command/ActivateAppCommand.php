<?php declare(strict_types=1);

namespace Laser\Core\Framework\App\Command;

use Laser\Core\Framework\App\AppStateService;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\Log\Package;
use Symfony\Component\Console\Attribute\AsCommand;

/**
 * @internal only for use by the app-system, will be considered internal from v6.4.0 onward
 */
#[AsCommand(
    name: 'app:activate',
    description: 'Deactivates an app',
)]
#[Package('core')]
class ActivateAppCommand extends AbstractAppActivationCommand
{
    private const ACTION = 'activate';

    public function __construct(
        EntityRepository $appRepo,
        private readonly AppStateService $appStateService
    ) {
        parent::__construct($appRepo, self::ACTION);
    }

    public function runAction(string $appId, Context $context): void
    {
        $this->appStateService->activateApp($appId, $context);
    }
}
