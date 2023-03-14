<?php declare(strict_types=1);

namespace Laser\Core\System\SalesChannel;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\ArrayStruct;

#[Package('core')]
class SuccessResponse extends StoreApiResponse
{
    /**
     * @var ArrayStruct<string, mixed>
     */
    protected $object;

    public function __construct()
    {
        parent::__construct(new ArrayStruct(['success' => true]));
    }
}
