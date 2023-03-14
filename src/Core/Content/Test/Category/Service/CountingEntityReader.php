<?php declare(strict_types=1);

namespace Laser\Core\Content\Test\Category\Service;

use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\Entity;
use Laser\Core\Framework\DataAbstractionLayer\EntityCollection;
use Laser\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Laser\Core\Framework\DataAbstractionLayer\Read\EntityReaderInterface;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;

/**
 * @internal
 */
class CountingEntityReader implements EntityReaderInterface
{
    /**
     * @var int[]
     */
    private static array $count = [];

    public function __construct(private readonly EntityReaderInterface $inner)
    {
    }

    /**
     * @return EntityCollection<Entity>
     */
    public function read(EntityDefinition $definition, Criteria $criteria, Context $context): EntityCollection
    {
        static::$count[$definition->getEntityName()] ??= 0 + 1;

        return $this->inner->read($definition, $criteria, $context);
    }

    public static function resetCount(): void
    {
        static::$count = [];
    }

    public static function getReadOperationCount(string $entityName): int
    {
        return static::$count[$entityName] ?? 0;
    }
}
