<?php declare(strict_types=1);

namespace Laser\Core\Framework\Adapter\Translation;

use Laser\Core\Framework\Log\Package;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Package('core')]
abstract class AbstractTranslator implements TranslatorInterface, TranslatorBagInterface, LocaleAwareInterface
{
    abstract public function getDecorated(): AbstractTranslator;

    /**
     * @return mixed|null All kind of data could be cached
     */
    abstract public function trace(string $key, \Closure $param);

    /**
     * @return array<int, string>
     */
    abstract public function getTrace(string $key): array;
}
