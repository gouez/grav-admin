<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart;

use Laser\Core\Content\Rule\RuleCollection;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Log\Package;
use Symfony\Contracts\Cache\CacheInterface;

#[Package('checkout')]
class CachedRuleLoader extends AbstractRuleLoader
{
    final public const CACHE_KEY = 'cart_rules';

    /**
     * @internal
     */
    public function __construct(
        private readonly AbstractRuleLoader $decorated,
        private readonly CacheInterface $cache
    ) {
    }

    public function getDecorated(): AbstractRuleLoader
    {
        return $this->decorated;
    }

    public function load(Context $context): RuleCollection
    {
        return $this->cache->get(self::CACHE_KEY, fn (): RuleCollection => $this->decorated->load($context));
    }
}
