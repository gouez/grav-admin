<?php declare(strict_types=1);

namespace Core\System\Test\SystemConfig;

use PHPUnit\Framework\TestCase;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Test\TestCaseBase\DatabaseTransactionBehaviour;
use Laser\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;
use Laser\Core\System\SystemConfig\SystemConfigCollection;

/**
 * @internal
 */
#[Package('system-settings')]
class SystemConfigRepositoryTest extends TestCase
{
    use KernelTestBehaviour;
    use DatabaseTransactionBehaviour;

    private const CONFIG_KEY = 'testKey';
    private const CONFIG_VALUE = 'testValue';

    public function testFilterByValue(): void
    {
        /** @var EntityRepository $repo */
        $repo = $this->getContainer()->get('system_config.repository');
        $context = Context::createDefaultContext();

        $data = [[
            'configurationKey' => self::CONFIG_KEY,
            'configurationValue' => self::CONFIG_VALUE,
        ]];

        $repo->upsert($data, $context);

        $filterByKeyCriteria = (new Criteria())->addFilter(new EqualsFilter('configurationKey', self::CONFIG_KEY));
        /** @var SystemConfigCollection $configs */
        $configs = $repo->search($filterByKeyCriteria, $context)->getEntities();
        static::assertCount(1, $configs);

        $firstConfig = $configs->first();
        static::assertNotNull($firstConfig);
        static::assertSame(self::CONFIG_VALUE, $firstConfig->getConfigurationValue());

        $filterByValueCriteria = (new Criteria())->addFilter(new EqualsFilter('configurationValue', self::CONFIG_VALUE));
        /** @var SystemConfigCollection $configs */
        $configs = $repo->search($filterByValueCriteria, $context)->getEntities();
        static::assertCount(1, $configs);

        $firstConfig = $configs->first();
        static::assertNotNull($firstConfig);
        static::assertSame(self::CONFIG_KEY, $firstConfig->getConfigurationKey());
    }
}
