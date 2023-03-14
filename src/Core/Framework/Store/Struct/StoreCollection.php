<?php declare(strict_types=1);

namespace Laser\Core\Framework\Store\Struct;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\Collection;

/**
 * @codeCoverageIgnore
 *
 * @extends Collection<StoreStruct>
 */
#[Package('merchant-services')]
abstract class StoreCollection extends Collection
{
    public function __construct(iterable $elements = [])
    {
        foreach ($elements as $element) {
            if (\is_array($element)) {
                $element = $this->getElementFromArray($element);
            }

            $this->add($element);
        }
    }

    protected function getExpectedClass(): ?string
    {
        return ExtensionStruct::class;
    }

    abstract protected function getElementFromArray(array $element): StoreStruct;
}
