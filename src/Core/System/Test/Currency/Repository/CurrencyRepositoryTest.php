<?php declare(strict_types=1);

namespace Laser\Core\System\Test\Currency\Repository;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Laser\Core\Defaults;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Pricing\CashRoundingConfig;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\Term\EntityScoreQueryBuilder;
use Laser\Core\Framework\DataAbstractionLayer\Search\Term\SearchTermInterpreter;
use Laser\Core\Framework\DataAbstractionLayer\Write\Validation\RestrictDeleteViolationException;
use Laser\Core\Framework\Test\TestCaseBase\DatabaseTransactionBehaviour;
use Laser\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\System\Currency\CurrencyDefinition;

/**
 * @internal
 */
class CurrencyRepositoryTest extends TestCase
{
    use KernelTestBehaviour;
    use DatabaseTransactionBehaviour;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var EntityRepository
     */
    private $currencyRepository;

    protected function setUp(): void
    {
        $this->currencyRepository = $this->getContainer()->get('currency.repository');
        $this->connection = $this->getContainer()->get(Connection::class);
    }

    public function testSearchRanking(): void
    {
        $recordA = Uuid::randomHex();
        $recordB = Uuid::randomHex();

        $records = [
            [
                'id' => $recordA,
                'decimalPrecision' => 2,
                'name' => 'match',
                'isoCode' => 'FOO',
                'shortName' => 'test',
                'factor' => 1,
                'symbol' => 'A',
                'itemRounding' => json_decode(json_encode(new CashRoundingConfig(2, 0.01, true), \JSON_THROW_ON_ERROR), true, 512, \JSON_THROW_ON_ERROR),
                'totalRounding' => json_decode(json_encode(new CashRoundingConfig(2, 0.01, true), \JSON_THROW_ON_ERROR), true, 512, \JSON_THROW_ON_ERROR),
            ],
            [
                'id' => $recordB,
                'decimalPrecision' => 2,
                'name' => 'not',
                'isoCode' => 'BAR',
                'shortName' => 'match',
                'factor' => 1,
                'symbol' => 'A',
                'itemRounding' => json_decode(json_encode(new CashRoundingConfig(2, 0.01, true), \JSON_THROW_ON_ERROR), true, 512, \JSON_THROW_ON_ERROR),
                'totalRounding' => json_decode(json_encode(new CashRoundingConfig(2, 0.01, true), \JSON_THROW_ON_ERROR), true, 512, \JSON_THROW_ON_ERROR),
            ],
        ];

        $this->currencyRepository->create($records, Context::createDefaultContext());

        $criteria = new Criteria();

        $builder = $this->getContainer()->get(EntityScoreQueryBuilder::class);
        $pattern = $this->getContainer()->get(SearchTermInterpreter::class)->interpret('match');
        $context = Context::createDefaultContext();
        $queries = $builder->buildScoreQueries(
            $pattern,
            $this->currencyRepository->getDefinition(),
            $this->currencyRepository->getDefinition()->getEntityName(),
            $context
        );
        $criteria->addQuery(...$queries);

        $result = $this->currencyRepository->searchIds($criteria, Context::createDefaultContext());

        static::assertCount(2, $result->getIds());

        static::assertEquals(
            [$recordA, $recordB],
            $result->getIds()
        );

        static::assertGreaterThan(
            $result->getDataFieldOfId($recordB, '_score'),
            $result->getDataFieldOfId($recordA, '_score')
        );
    }

    public function testDeleteNonDefaultCurrency(): void
    {
        $context = Context::createDefaultContext();
        $recordA = Uuid::randomHex();

        $records = [
            [
                'id' => $recordA,
                'decimalPrecision' => 2,
                'name' => 'match',
                'isoCode' => 'FOO',
                'shortName' => 'test',
                'factor' => 1,
                'symbol' => 'A',
                'itemRounding' => json_decode(json_encode(new CashRoundingConfig(2, 0.01, true), \JSON_THROW_ON_ERROR), true, 512, \JSON_THROW_ON_ERROR),
                'totalRounding' => json_decode(json_encode(new CashRoundingConfig(2, 0.01, true), \JSON_THROW_ON_ERROR), true, 512, \JSON_THROW_ON_ERROR),
            ],
        ];

        $this->currencyRepository->create($records, $context);

        $deleteEvent = $this->currencyRepository->delete([['id' => $recordA]], $context);

        static::assertEquals($recordA, $deleteEvent->getEventByEntityName(CurrencyDefinition::ENTITY_NAME)->getWriteResults()[0]->getPrimaryKey());
    }

    public function testDeleteDefaultCurrency(): void
    {
        $context = Context::createDefaultContext();

        $this->expectException(RestrictDeleteViolationException::class);
        $this->currencyRepository->delete([['id' => Defaults::CURRENCY]], $context);
    }
}
