<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\Routing;

use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\Cart\Exception\CustomerNotLoggedInException;
use Laser\Core\Checkout\Test\Customer\SalesChannel\CustomerTestTrait;
use Laser\Core\Defaults;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Laser\Core\Framework\Routing\Event\SalesChannelContextResolvedEvent;
use Laser\Core\Framework\Routing\SalesChannelRequestContextResolver;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Laser\Core\Framework\Test\TestDataCollection;
use Laser\Core\Framework\Util\Random;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\PlatformRequest;
use Laser\Core\SalesChannelRequest;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextPersister;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextService;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextServiceInterface;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextServiceParameters;
use Laser\Core\Test\TestDefaults;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use function print_r;

/**
 * @internal
 */
class SalesChannelRequestContextResolverTest extends TestCase
{
    use IntegrationTestBehaviour;
    use CustomerTestTrait;

    private TestDataCollection $ids;

    private EntityRepository $currencyRepository;

    private SalesChannelContextServiceInterface $contextService;

    public function setUp(): void
    {
        $this->ids = new TestDataCollection();
        $this->currencyRepository = $this->getContainer()->get('currency.repository');
        $this->contextService = $this->getContainer()->get(SalesChannelContextService::class);
    }

    public function testRequestSalesChannelCurrency(): void
    {
        $this->createTestSalesChannel();
        $resolver = $this->getContainer()->get(SalesChannelRequestContextResolver::class);

        $phpunit = $this;
        $currencyId = $this->getCurrencyId('USD');

        $request = new Request();
        $request->attributes->set(PlatformRequest::ATTRIBUTE_SALES_CHANNEL_ID, $this->ids->get('sales-channel'));
        $request->attributes->set(SalesChannelRequest::ATTRIBUTE_DOMAIN_CURRENCY_ID, $currencyId);
        $request->attributes->set(PlatformRequest::ATTRIBUTE_ROUTE_SCOPE, ['store-api']);

        /** @var EventDispatcher $dispatcher */
        $dispatcher = $this->getContainer()->get('event_dispatcher');

        $eventDidRun = false;
        $listenerContextEventClosure = function (SalesChannelContextResolvedEvent $event) use (&$eventDidRun, $phpunit, $currencyId): void {
            $eventDidRun = true;
            $phpunit->assertSame($currencyId, $event->getSalesChannelContext()->getContext()->getCurrencyId());
        };

        $this->addEventListener($dispatcher, SalesChannelContextResolvedEvent::class, $listenerContextEventClosure);

        $resolver->resolve($request);

        $dispatcher->removeListener(SalesChannelContextResolvedEvent::class, $listenerContextEventClosure);

        static::assertTrue($eventDidRun, 'The "' . SalesChannelContextResolvedEvent::class . '" Event did not run');
    }

    /**
     * @dataProvider domainData
     */
    public function testContextCurrency(string $url, string $currencyCode, string $expectedCode): void
    {
        $this->createTestSalesChannel();
        $currencyId = $this->getCurrencyId($currencyCode);
        $expectedCurrencyId = $expectedCode !== $currencyCode ? $this->getCurrencyId($expectedCode) : $currencyId;

        $context = $this->contextService->get(
            new SalesChannelContextServiceParameters($this->ids->get('sales-channel'), $this->ids->get('token'), Defaults::LANGUAGE_SYSTEM, $currencyId)
        );

        static::assertSame($expectedCurrencyId, $context->getContext()->getCurrencyId());
    }

    /**
     * @return list<array{0: string, 1: string, 2: string}>
     */
    public static function domainData(): array
    {
        return [
            [
                'http://test.store/en-eur',
                'EUR',
                'EUR',
            ],
            [
                'http://test.store/en-usd',
                'USD',
                'USD',
            ],
        ];
    }

    /**
     * @dataProvider loginRequiredAnnotationData
     *
     * @param array<string, bool> $attributes
     */
    public function testLoginRequiredAnnotation(bool $doLogin, bool $isGuest, array $attributes, bool $pass): void
    {
        $resolver = $this->getContainer()->get(SalesChannelRequestContextResolver::class);

        $currencyId = $this->getCurrencyId('USD');

        $request = new Request();
        $request->attributes->set(PlatformRequest::ATTRIBUTE_SALES_CHANNEL_ID, TestDefaults::SALES_CHANNEL);
        $request->attributes->set(SalesChannelRequest::ATTRIBUTE_DOMAIN_CURRENCY_ID, $currencyId);
        $request->attributes->set(PlatformRequest::ATTRIBUTE_ROUTE_SCOPE, ['store-api']);

        if ($doLogin) {
            $request->headers->set(PlatformRequest::HEADER_CONTEXT_TOKEN, $this->loginCustomer($isGuest));
        }

        foreach ($attributes as $k => $v) {
            $request->attributes->set($k, $v);
        }

        $exception = null;

        try {
            $resolver->resolve($request);
        } catch (\Exception $e) {
            $exception = $e;
        }

        if ($pass) {
            static::assertNull($exception, 'Exception: ' . ($exception !== null ? print_r($exception->getMessage(), true) : 'No Exception'));
        } else {
            static::assertInstanceOf(CustomerNotLoggedInException::class, $exception, 'Exception: ' . ($exception !== null ? print_r($exception->getMessage(), true) : 'No Exception'));
        }
    }

    /**
     * @return list<array{0: bool, 1: bool, 2: array<string, bool>, 3: bool}>
     */
    public static function loginRequiredAnnotationData(): array
    {
        $loginRequiredNotAllowGuest = [PlatformRequest::ATTRIBUTE_LOGIN_REQUIRED => true];
        $loginRequiredAllowGuest = [PlatformRequest::ATTRIBUTE_LOGIN_REQUIRED => true, PlatformRequest::ATTRIBUTE_LOGIN_REQUIRED_ALLOW_GUEST => true];

        return [
            [
                true, // login
                true, // guest
                $loginRequiredNotAllowGuest, // annotation
                false, // pass
            ],
            [
                true,
                false,
                $loginRequiredNotAllowGuest,
                true,
            ],
            [
                false,
                false,
                $loginRequiredNotAllowGuest,
                false,
            ],
            [
                false,
                true,
                $loginRequiredNotAllowGuest,
                false,
            ],
            [
                true,
                true,
                $loginRequiredAllowGuest,
                true,
            ],
            [
                true,
                false,
                $loginRequiredAllowGuest,
                true,
            ],
            [
                false,
                false,
                $loginRequiredAllowGuest,
                false,
            ],
            [
                false,
                true,
                $loginRequiredAllowGuest,
                false,
            ],

            [
                true,
                false,
                [],
                true,
            ],
            [
                false,
                false,
                [],
                true,
            ],
            [
                true,
                true,
                [],
                true,
            ],
            [
                false,
                true,
                [],
                true,
            ],
        ];
    }

    private function loginCustomer(bool $isGuest): string
    {
        $email = Uuid::randomHex() . '@example.com';
        $password = 'laser';
        $customerId = $this->createCustomer($password, $email, $isGuest);

        $token = Random::getAlphanumericString(32);
        $this->getContainer()->get(SalesChannelContextPersister::class)->save($token, ['customerId' => $customerId], TestDefaults::SALES_CHANNEL);

        return $token;
    }

    private function getCurrencyId(string $isoCode): ?string
    {
        return $this->currencyRepository->searchIds(
            (new Criteria())->addFilter(new EqualsFilter('isoCode', $isoCode)),
            Context::createDefaultContext()
        )->firstId();
    }

    private function createTestSalesChannel(): void
    {
        $usdCurrencyId = $this->getCurrencyId('USD');

        $this->createSalesChannel([
            'id' => $this->ids->create('sales-channel'),
            'domains' => [
                [
                    'id' => $this->ids->get('eur-domain'),
                    'url' => 'http://test.store/en-eur',
                    'languageId' => Defaults::LANGUAGE_SYSTEM,
                    'currencyId' => Defaults::CURRENCY,
                    'snippetSetId' => $this->getSnippetSetIdForLocale('en-GB'),
                ],
                [
                    'id' => $this->ids->get('usd-domain'),
                    'url' => 'http://test.store/en-usd',
                    'languageId' => Defaults::LANGUAGE_SYSTEM,
                    'currencyId' => $usdCurrencyId,
                    'snippetSetId' => $this->getSnippetSetIdForLocale('en-GB'),
                ],
            ],
        ]);
    }
}
