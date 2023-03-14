<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Document\FileGenerator;

use Laser\Core\Framework\Log\Package;

#[Package('customer-order')]
class FileTypes
{
    final public const PDF = 'pdf';
}
