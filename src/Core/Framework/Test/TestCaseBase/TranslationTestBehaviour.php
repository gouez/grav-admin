<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\TestCaseBase;

use Laser\Core\Framework\Adapter\Translation\Translator;
use Symfony\Component\DependencyInjection\ContainerInterface;

trait TranslationTestBehaviour
{
    /**
     * @before
     *
     * @after
     */
    public function resetInjectedTranslatorSettings(): void
    {
        /** @var Translator $translator */
        $translator = $this->getContainer()->get(Translator::class);

        // reset injected settings to make tests deterministic
        $translator->resetInjection();
    }

    abstract protected static function getContainer(): ContainerInterface;
}
