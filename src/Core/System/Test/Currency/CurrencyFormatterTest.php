<?php declare(strict_types=1);

namespace Laser\Core\System\Test\Currency;

use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\Cart\Price\Struct\CartPrice;
use Laser\Core\Defaults;
use Laser\Core\Framework\Api\Context\SystemSource;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\Pricing\CashRoundingConfig;
use Laser\Core\Framework\Test\TestCaseBase\BasicTestDataBehaviour;
use Laser\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;
use Laser\Core\System\Currency\CurrencyFormatter;

/**
 * @internal
 */
class CurrencyFormatterTest extends TestCase
{
    use KernelTestBehaviour;
    use BasicTestDataBehaviour;

    public function testFormatByLanguage(): void
    {
        $currencyFormatter = $this->getContainer()->get(CurrencyFormatter::class);

        $price = (float) '132582.98765432';
        $context = Context::createDefaultContext();

        $deLanguageId = $this->getDeDeLanguageId();

        $formattedCurrency = $currencyFormatter->formatCurrencyByLanguage(
            $price,
            'EUR',
            $deLanguageId,
            $context
        );

        static::assertSame('132.582,99 €', $formattedCurrency);

        $formattedCurrency = $currencyFormatter->formatCurrencyByLanguage(
            $price,
            'EUR',
            Defaults::LANGUAGE_SYSTEM,
            $context
        );

        static::assertSame('€132,582.99', $formattedCurrency);

        $formattedCurrency = $currencyFormatter->formatCurrencyByLanguage(
            $price,
            'USD',
            $deLanguageId,
            $context
        );

        static::assertSame('132.582,99 $', $formattedCurrency);

        $formattedCurrency = $currencyFormatter->formatCurrencyByLanguage(
            $price,
            'USD',
            Defaults::LANGUAGE_SYSTEM,
            $context
        );

        static::assertSame('US$132,582.99', $formattedCurrency);
    }

    /**
     * @dataProvider digitProvider
     */
    public function testDigits(float $price, int $digits, string $expected): void
    {
        $formatter = $this->getContainer()->get(CurrencyFormatter::class);

        $context = new Context(
            new SystemSource(),
            [],
            Defaults::CURRENCY,
            [Defaults::LANGUAGE_SYSTEM],
            Defaults::LIVE_VERSION,
            1,
            true,
            CartPrice::TAX_STATE_GROSS,
            new CashRoundingConfig($digits, 0.01, true)
        );

        $languageId = $this->getDeDeLanguageId();

        $formatted = $formatter->formatCurrencyByLanguage($price, 'EUR', $languageId, $context, $digits);

        static::assertEquals($expected, $formatted);
    }

    /**
     * @return array<array<float|int|string>>
     */
    public static function digitProvider(): array
    {
        return [
            [19.9999, 2, '20,00 €'],
            [19.9999, 3, '20,000 €'],
            [19.9999, 4, '19,9999 €'],
        ];
    }

    /**
     * @return array<array<float|int|string>>
     */
    public function digitWithFeatureProvider(): array
    {
        return [
            [19.9999, 2, '20,00 €'],
            [19.9999, 3, '20,000 €'],
            [19.9999, 4, '19,9999 €'],
        ];
    }
}
