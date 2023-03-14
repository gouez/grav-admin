<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Document;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\Collection;

/**
 * @extends Collection<DocumentIdStruct>
 */
#[Package('customer-order')]
class DocumentIdCollection extends Collection
{
    public function getApiAlias(): string
    {
        return 'document_id_collection';
    }

    protected function getExpectedClass(): ?string
    {
        return DocumentIdStruct::class;
    }
}
