<?php declare(strict_types=1);

namespace Laser\Core\System\Currency;

use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\Exception\InconsistentCriteriaIdsException;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Routing\Exception\LanguageNotFoundException;
use Laser\Core\System\Locale\LanguageLocaleCodeProvider;

#[Package('inventory')]
class CurrencyFormatter
{
    /**
     * @var \NumberFormatter[]
     */
    private array $formatter = [];

    /**
     * @internal
     */
    public function __construct(private readonly LanguageLocaleCodeProvider $languageLocaleProvider)
    {
    }

    /**
     * @throws InconsistentCriteriaIdsException
     * @throws LanguageNotFoundException
     */
    public function formatCurrencyByLanguage(float $price, string $currency, string $languageId, Context $context, ?int $decimals = null): string
    {
        $decimals ??= $context->getRounding()->getDecimals();

        $locale = $this->languageLocaleProvider->getLocaleForLanguageId($languageId);
        $formatter = $this->getFormatter($locale, \NumberFormatter::CURRENCY);
        $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, $decimals);

        return (string) $formatter->formatCurrency($price, $currency);
    }

    private function getFormatter(string $locale, int $format): \NumberFormatter
    {
        $hash = md5(json_encode([$locale, $format], \JSON_THROW_ON_ERROR));

        if (isset($this->formatter[$hash])) {
            return $this->formatter[$hash];
        }

        return $this->formatter[$hash] = new \NumberFormatter($locale, $format);
    }
}
