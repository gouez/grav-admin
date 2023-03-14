<?php declare(strict_types=1);

namespace Laser\Core\Framework\Demodata;

use Laser\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
interface DemodataGeneratorInterface
{
    public function getDefinition(): string;

    /**
     * @param array<string, mixed> $options
     */
    public function generate(int $numberOfItems, DemodataContext $context, array $options = []): void;
}
