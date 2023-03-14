<?php declare(strict_types=1);

namespace Laser\Core\Framework\App\Payment\Response;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\Struct;

/**
 * @internal only for use by the app-system
 */
#[Package('core')]
abstract class AbstractResponse extends Struct
{
    final public function __construct()
    {
    }

    abstract public function validate(string $transactionId): void;

    public static function create(?string $transactionId, array $data): self
    {
        $response = new static();
        $response->assign($data);
        if ($transactionId) {
            $response->validate($transactionId);
        }

        return $response;
    }
}
