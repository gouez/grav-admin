<?php declare(strict_types=1);

namespace Laser\Core\System\Language\SalesChannel;

use Laser\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\Language\LanguageCollection;
use Laser\Core\System\SalesChannel\StoreApiResponse;

#[Package('system-settings')]
class LanguageRouteResponse extends StoreApiResponse
{
    /**
     * @var EntitySearchResult
     */
    protected $object;

    public function __construct(EntitySearchResult $languages)
    {
        parent::__construct($languages);
    }

    public function getLanguages(): LanguageCollection
    {
        /** @var LanguageCollection $collection */
        $collection = $this->object->getEntities();

        return $collection;
    }
}
