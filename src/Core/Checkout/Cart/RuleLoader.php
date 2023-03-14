<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart;

use Laser\Core\Content\Rule\RuleCollection;
use Laser\Core\Content\Rule\RuleEntity;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\Dbal\Common\RepositoryIterator;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Plugin\Exception\DecorationPatternException;

/**
 * @internal
 */
#[Package('checkout')]
class RuleLoader extends AbstractRuleLoader
{
    public function __construct(private readonly EntityRepository $repository)
    {
    }

    public function getDecorated(): AbstractRuleLoader
    {
        throw new DecorationPatternException(self::class);
    }

    public function load(Context $context): RuleCollection
    {
        $criteria = new Criteria();
        $criteria->addSorting(new FieldSorting('priority', FieldSorting::DESCENDING));
        $criteria->addSorting(new FieldSorting('id'));
        $criteria->setLimit(500);
        $criteria->setTitle('cart-rule-loader::load-rules');

        $repositoryIterator = new RepositoryIterator($this->repository, $context, $criteria);
        $rules = new RuleCollection();
        while (($result = $repositoryIterator->fetch()) !== null) {
            /** @var RuleEntity $rule */
            foreach ($result->getEntities() as $rule) {
                if (!$rule->isInvalid() && $rule->getPayload()) {
                    $rules->add($rule);
                }
            }
            if ($result->count() < 500) {
                break;
            }
        }

        return $rules;
    }
}
