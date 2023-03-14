<?php declare(strict_types=1);

namespace Laser\Core\Content\Test\Rule\DataAbstractionLayer;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Laser\Core\Content\Rule\DataAbstractionLayer\RuleIndexer;
use Laser\Core\Defaults;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexingMessage;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Migration\MigrationCollection;
use Laser\Core\Framework\Migration\MigrationRuntime;
use Laser\Core\Framework\Migration\MigrationSource;
use Laser\Core\Framework\Plugin;
use Laser\Core\Framework\Plugin\Context\ActivateContext;
use Laser\Core\Framework\Plugin\Context\DeactivateContext;
use Laser\Core\Framework\Plugin\Context\InstallContext;
use Laser\Core\Framework\Plugin\Context\UninstallContext;
use Laser\Core\Framework\Plugin\Context\UpdateContext;
use Laser\Core\Framework\Plugin\Event\PluginLifecycleEvent;
use Laser\Core\Framework\Plugin\Event\PluginPostActivateEvent;
use Laser\Core\Framework\Plugin\Event\PluginPostDeactivateEvent;
use Laser\Core\Framework\Plugin\Event\PluginPostInstallEvent;
use Laser\Core\Framework\Plugin\Event\PluginPostUninstallEvent;
use Laser\Core\Framework\Plugin\Event\PluginPostUpdateEvent;
use Laser\Core\Framework\Plugin\PluginEntity;
use Laser\Core\Framework\Rule\Container\AndRule;
use Laser\Core\Framework\Rule\Container\OrRule;
use Laser\Core\Framework\Rule\Rule;
use Laser\Core\Framework\Rule\SalesChannelRule;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\Migration\Test\NullConnection;
use Laser\Core\System\Currency\Rule\CurrencyRule;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 */
#[Package('business-ops')]
class RulePayloadIndexerTest extends TestCase
{
    use IntegrationTestBehaviour;

    private Context $context;

    /**
     * @var EntityRepository
     */
    private $repository;

    /**
     * @var RuleIndexer
     */
    private $indexer;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    protected function setUp(): void
    {
        $this->repository = $this->getContainer()->get('rule.repository');
        $this->indexer = $this->getContainer()->get(RuleIndexer::class);
        $this->connection = $this->getContainer()->get(Connection::class);
        $this->context = Context::createDefaultContext();
        $this->eventDispatcher = $this->getContainer()->get('event_dispatcher');
    }

    public function testIndex(): void
    {
        $id = Uuid::randomHex();
        $currencyId1 = Uuid::randomHex();
        $currencyId2 = Uuid::randomHex();

        $data = [
            'id' => $id,
            'name' => 'test rule',
            'priority' => 1,
            'conditions' => [
                [
                    'type' => (new OrRule())->getName(),
                    'children' => [
                        [
                            'type' => (new CurrencyRule())->getName(),
                            'value' => [
                                'currencyIds' => [
                                    $currencyId1,
                                    $currencyId2,
                                ],
                                'operator' => CurrencyRule::OPERATOR_EQ,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->repository->create([$data], $this->context);

        $this->connection->update('rule', ['payload' => null, 'invalid' => '1'], ['HEX(1)' => '1']);
        $rule = $this->repository->search(new Criteria([$id]), $this->context)->get($id);
        static::assertNull($rule->get('payload'));

        $this->indexer->handle(new EntityIndexingMessage([$id]));

        $rule = $this->repository->search(new Criteria([$id]), $this->context)->get($id);
        static::assertNotNull($rule->getPayload());
        static::assertInstanceOf(Rule::class, $rule->getPayload());
        static::assertEquals(
            new AndRule([new OrRule([(new CurrencyRule())->assign(['currencyIds' => [$currencyId1, $currencyId2]])])]),
            $rule->getPayload()
        );
    }

    public function testRefresh(): void
    {
        $id = Uuid::randomHex();
        $currencyId1 = Uuid::randomHex();
        $currencyId2 = Uuid::randomHex();

        $data = [
            'id' => $id,
            'name' => 'test rule',
            'priority' => 1,
            'conditions' => [
                [
                    'type' => (new OrRule())->getName(),
                    'children' => [
                        [
                            'type' => (new CurrencyRule())->getName(),
                            'value' => [
                                'currencyIds' => [
                                    $currencyId1,
                                    $currencyId2,
                                ],
                                'operator' => CurrencyRule::OPERATOR_EQ,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->repository->create([$data], $this->context);

        $rule = $this->repository->search(new Criteria([$id]), $this->context)->get($id);
        static::assertNotNull($rule->getPayload());
        static::assertInstanceOf(Rule::class, $rule->getPayload());
        static::assertEquals(
            new AndRule([new OrRule([(new CurrencyRule())->assign(['currencyIds' => [$currencyId1, $currencyId2]])])]),
            $rule->getPayload()
        );
    }

    public function testRefreshWithMultipleRules(): void
    {
        $id = Uuid::randomHex();
        $rule2Id = Uuid::randomHex();
        $currencyId1 = Uuid::randomHex();
        $currencyId2 = Uuid::randomHex();
        $salesChannelId1 = Uuid::randomHex();
        $salesChannelId2 = Uuid::randomHex();

        $data = [
            [
                'id' => $id,
                'name' => 'test rule',
                'priority' => 1,
                'conditions' => [
                    [
                        'type' => (new OrRule())->getName(),
                        'children' => [
                            [
                                'type' => (new CurrencyRule())->getName(),
                                'value' => [
                                    'currencyIds' => [
                                        $currencyId1,
                                        $currencyId2,
                                    ],
                                    'operator' => CurrencyRule::OPERATOR_EQ,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'id' => $rule2Id,
                'name' => 'second rule',
                'priority' => 42,
                'conditions' => [
                    [
                        'type' => (new SalesChannelRule())->getName(),
                        'value' => [
                            'salesChannelIds' => [
                                $salesChannelId1,
                                $salesChannelId2,
                            ],
                            'operator' => CurrencyRule::OPERATOR_EQ,
                        ],
                    ],
                ],
            ],
        ];

        $this->repository->create($data, $this->context);

        $this->connection->update('rule', ['payload' => null, 'invalid' => '1'], ['HEX(1)' => '1']);
        $rule = $this->repository->search(new Criteria([$id]), $this->context)->get($id);
        static::assertNull($rule->get('payload'));

        $this->indexer->handle(new EntityIndexingMessage([$id, $rule2Id]));

        $rules = $this->repository->search(new Criteria([$id, $rule2Id]), $this->context);
        $rule = $rules->get($id);
        static::assertNotNull($rule->getPayload());
        static::assertInstanceOf(Rule::class, $rule->getPayload());
        static::assertEquals(
            new AndRule([new OrRule([(new CurrencyRule())->assign(['currencyIds' => [$currencyId1, $currencyId2]])])]),
            $rule->getPayload()
        );
        $rule = $rules->get($rule2Id);
        static::assertNotNull($rule->getPayload());
        static::assertInstanceOf(Rule::class, $rule->getPayload());
        static::assertEquals(
            new AndRule([(new SalesChannelRule())->assign(['salesChannelIds' => [$salesChannelId1, $salesChannelId2]])]),
            $rule->getPayload()
        );
    }

    public function testIndexWithMultipleRules(): void
    {
        $id = Uuid::randomHex();
        $rule2Id = Uuid::randomHex();
        $currencyId1 = Uuid::randomHex();
        $currencyId2 = Uuid::randomHex();
        $salesChannelId1 = Uuid::randomHex();
        $salesChannelId2 = Uuid::randomHex();

        $data = [
            [
                'id' => $id,
                'name' => 'test rule',
                'priority' => 1,
                'conditions' => [
                    [
                        'type' => (new OrRule())->getName(),
                        'children' => [
                            [
                                'type' => (new CurrencyRule())->getName(),
                                'value' => [
                                    'currencyIds' => [
                                        $currencyId1,
                                        $currencyId2,
                                    ],
                                    'operator' => CurrencyRule::OPERATOR_EQ,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'id' => $rule2Id,
                'name' => 'second rule',
                'priority' => 42,
                'conditions' => [
                    [
                        'type' => (new SalesChannelRule())->getName(),
                        'value' => [
                            'salesChannelIds' => [
                                $salesChannelId1,
                                $salesChannelId2,
                            ],
                            'operator' => SalesChannelRule::OPERATOR_EQ,
                        ],
                    ],
                ],
            ],
        ];

        $this->repository->create($data, $this->context);

        $rules = $this->repository->search(new Criteria([$id, $rule2Id]), $this->context);
        $rule = $rules->get($id);
        static::assertNotNull($rule->getPayload());
        static::assertInstanceOf(Rule::class, $rule->getPayload());
        static::assertEquals(
            new AndRule([new OrRule([(new CurrencyRule())->assign(['currencyIds' => [$currencyId1, $currencyId2]])])]),
            $rule->getPayload()
        );
        $rule = $rules->get($rule2Id);
        static::assertNotNull($rule->getPayload());
        static::assertInstanceOf(Rule::class, $rule->getPayload());
        static::assertEquals(
            new AndRule([(new SalesChannelRule())->assign(['salesChannelIds' => [$salesChannelId1, $salesChannelId2]])]),
            $rule->getPayload()
        );
    }

    public function testIndexWithMultipleRootConditions(): void
    {
        $id = Uuid::randomHex();

        $data = [
            'id' => $id,
            'name' => 'test rule',
            'priority' => 1,
            'conditions' => [
                [
                    'type' => (new OrRule())->getName(),
                    'children' => [
                        [
                            'type' => (new AndRule())->getName(),
                            'children' => [
                                [
                                    'type' => (new CurrencyRule())->getName(),
                                    'value' => [
                                        'currencyIds' => [
                                            Uuid::randomHex(),
                                            Uuid::randomHex(),
                                        ],
                                        'operator' => CurrencyRule::OPERATOR_EQ,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'type' => (new OrRule())->getName(),
                ],
            ],
        ];

        $this->repository->create([$data], $this->context);

        $this->connection->update('rule', ['payload' => null, 'invalid' => '1'], ['HEX(1)' => '1']);
        $rule = $this->repository->search(new Criteria([$id]), $this->context)->get($id);
        static::assertNull($rule->get('payload'));
        $this->indexer->handle(new EntityIndexingMessage([$id]));

        $rule = $this->repository->search(new Criteria([$id]), $this->context)->get($id);
        static::assertNotNull($rule->getPayload());
        static::assertInstanceOf(AndRule::class, $rule->getPayload());

        static::assertCount(2, $rule->getPayload()->getRules());
        static::assertContainsOnlyInstancesOf(OrRule::class, $rule->getPayload()->getRules());
    }

    public function testIndexWithRootRuleNotAndRule(): void
    {
        $id = Uuid::randomHex();
        $currencyId1 = Uuid::randomHex();
        $currencyId2 = Uuid::randomHex();

        $data = [
            'id' => $id,
            'name' => 'test rule',
            'priority' => 1,
            'conditions' => [
                [
                    'type' => (new CurrencyRule())->getName(),
                    'value' => [
                        'currencyIds' => [
                            $currencyId1,
                            $currencyId2,
                        ],
                        'operator' => CurrencyRule::OPERATOR_EQ,
                    ],
                ],
            ],
        ];

        $this->repository->create([$data], $this->context);

        $this->connection->update('rule', ['payload' => null, 'invalid' => '1'], ['HEX(1)' => '1']);
        $rule = $this->repository->search(new Criteria([$id]), $this->context)->get($id);
        static::assertNull($rule->get('payload'));

        $this->indexer->handle(new EntityIndexingMessage([$id]));

        $rule = $this->repository->search(new Criteria([$id]), $this->context)->get($id);
        static::assertNotNull($rule->getPayload());
        static::assertInstanceOf(Rule::class, $rule->getPayload());
        static::assertEquals(
            new AndRule([(new CurrencyRule())->assign(['currencyIds' => [$currencyId1, $currencyId2]])]),
            $rule->getPayload()
        );
    }

    public function testRefreshWithRootRuleNotAndRule(): void
    {
        $id = Uuid::randomHex();
        $currencyId1 = Uuid::randomHex();
        $currencyId2 = Uuid::randomHex();

        $data = [
            'id' => $id,
            'name' => 'test rule',
            'priority' => 1,
            'conditions' => [
                [
                    'type' => (new CurrencyRule())->getName(),
                    'value' => [
                        'currencyIds' => [
                            $currencyId1,
                            $currencyId2,
                        ],
                        'operator' => CurrencyRule::OPERATOR_EQ,
                    ],
                ],
            ],
        ];

        $this->repository->create([$data], $this->context);

        $rule = $this->repository->search(new Criteria([$id]), $this->context)->get($id);
        static::assertNotNull($rule->getPayload());
        static::assertInstanceOf(Rule::class, $rule->getPayload());
        static::assertEquals(
            new AndRule([(new CurrencyRule())->assign(['currencyIds' => [$currencyId1, $currencyId2]])]),
            $rule->getPayload()
        );
    }

    /**
     * @dataProvider dataProviderForTestPostEventNullsPayload
     */
    public function testPostEventNullsPayload(PluginLifecycleEvent $event): void
    {
        $payload = serialize(new AndRule());

        for ($i = 0; $i < 21; ++$i) {
            $this->connection->createQueryBuilder()
                ->insert('rule')
                ->values(['id' => ':id', 'name' => ':name', 'priority' => 1, 'payload' => ':payload', 'created_at' => ':createdAt'])
                ->setParameter('id', Uuid::randomBytes())
                ->setParameter('payload', $payload)
                ->setParameter('name', 'Rule' . $i)
                ->setParameter('createdAt', (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT))
                ->executeStatement();
        }

        $this->eventDispatcher->dispatch($event);

        $rules = $this->connection->createQueryBuilder()
            ->select(['id', 'payload', 'invalid'])
            ->from('rule')
            ->executeQuery()
            ->fetchAllAssociative();

        foreach ($rules as $rule) {
            static::assertEquals(0, $rule['invalid']);
            static::assertNull($rule['payload']);
            static::assertNotNull($rule['id']);
        }
    }

    public static function dataProviderForTestPostEventNullsPayload(): array
    {
        $plugin = new PluginEntity();
        $plugin->setName('TestPlugin');
        $plugin->setBaseClass(RulePlugin::class);
        $plugin->setPath('');

        $context = Context::createDefaultContext();
        $rulePlugin = new RulePlugin(false, '');

        $collection = new MigrationCollection(
            new MigrationSource('asd', []),
            new MigrationRuntime(new NullConnection(), new NullLogger()),
            new NullConnection()
        );

        return [
            [new PluginPostInstallEvent($plugin, new InstallContext($rulePlugin, $context, '', '', $collection))],
            [new PluginPostActivateEvent($plugin, new ActivateContext($rulePlugin, $context, '', '', $collection))],
            [new PluginPostUpdateEvent($plugin, new UpdateContext($rulePlugin, $context, '', '', $collection, ''))],
            [new PluginPostDeactivateEvent($plugin, new DeactivateContext($rulePlugin, $context, '', '', $collection))],
            [new PluginPostUninstallEvent($plugin, new UninstallContext($rulePlugin, $context, '', '', $collection, true))],
        ];
    }
}

/**
 * @internal
 */
#[Package('business-ops')]
class RulePlugin extends Plugin
{
}
