<?php declare(strict_types=1);

namespace Laser\Core\Framework\App\ActionButton\Response;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\Struct;

/**
 * @internal only for use by the app-system
 */
#[Package('core')]
abstract class ActionButtonResponse extends Struct
{
    public function __construct(protected string $actionType)
    {
    }
}
