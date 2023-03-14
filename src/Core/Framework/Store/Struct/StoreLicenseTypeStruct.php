<?php declare(strict_types=1);

namespace Laser\Core\Framework\Store\Struct;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\Struct;

/**
 * @codeCoverageIgnore
 */
#[Package('merchant-services')]
class StoreLicenseTypeStruct extends Struct
{
    /**
     * @var string
     */
    protected $name;

    public function getApiAlias(): string
    {
        return 'store_license_type';
    }
}
