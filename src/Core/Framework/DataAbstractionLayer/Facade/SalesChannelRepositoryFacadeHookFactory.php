<?php declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer\Facade;

use Laser\Core\Framework\DataAbstractionLayer\Search\RequestCriteriaBuilder;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Script\Exception\HookInjectionException;
use Laser\Core\Framework\Script\Execution\Awareness\HookServiceFactory;
use Laser\Core\Framework\Script\Execution\Awareness\SalesChannelContextAware;
use Laser\Core\Framework\Script\Execution\Hook;
use Laser\Core\Framework\Script\Execution\Script;
use Laser\Core\System\SalesChannel\Entity\SalesChannelDefinitionInstanceRegistry;

/**
 * @internal
 */
#[Package('core')]
class SalesChannelRepositoryFacadeHookFactory extends HookServiceFactory
{
    /**
     * @internal
     */
    public function __construct(
        private readonly SalesChannelDefinitionInstanceRegistry $registry,
        private readonly RequestCriteriaBuilder $criteriaBuilder
    ) {
    }

    public function factory(Hook $hook, Script $script): SalesChannelRepositoryFacade
    {
        if (!$hook instanceof SalesChannelContextAware) {
            throw new HookInjectionException($hook, self::class, SalesChannelContextAware::class);
        }

        return new SalesChannelRepositoryFacade(
            $this->registry,
            $this->criteriaBuilder,
            $hook->getSalesChannelContext()
        );
    }

    public function getName(): string
    {
        return 'store';
    }
}
