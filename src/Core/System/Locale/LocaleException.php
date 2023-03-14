<?php declare(strict_types=1);

namespace Laser\Core\System\Locale;

use Laser\Core\Framework\HttpException;
use Laser\Core\Framework\Log\Package;
use Symfony\Component\HttpFoundation\Response;

#[Package('system-settings')]
class LocaleException extends HttpException
{
    final public const LOCALE_DOES_NOT_EXISTS_EXCEPTION = 'SYSTEM__LOCALE_DOES_NOT_EXISTS';

    public static function localeDoesNotExists(string $locale): self
    {
        return new self(
            Response::HTTP_NOT_FOUND,
            self::LOCALE_DOES_NOT_EXISTS_EXCEPTION,
            'The locale {{ locale }} does not exists.',
            ['locale' => $locale]
        );
    }
}
