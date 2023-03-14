<?php declare(strict_types=1);

namespace Laser\Core\Content\ProductExport\Error;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\AssignArrayTrait;
use Laser\Core\Framework\Struct\CreateFromTrait;
use Laser\Core\Framework\Struct\JsonSerializableTrait;

#[Package('sales-channel')]
abstract class Error extends \Exception implements \JsonSerializable
{
    use JsonSerializableTrait;
    use AssignArrayTrait;
    use CreateFromTrait;

    abstract public function getId(): string;

    abstract public function getMessageKey(): string;

    abstract public function getParameters(): array;

    /**
     * @return ErrorMessage[]
     */
    abstract public function getErrorMessages(): array;

    public function jsonSerialize(): array
    {
        $data = get_object_vars($this);
        $data['key'] = $this->getId();
        $data['message'] = $this->getMessage();
        $data['messageKey'] = $this->getMessageKey();
        $data['errorMessages'] = $this->getErrorMessages();

        return $data;
    }
}
