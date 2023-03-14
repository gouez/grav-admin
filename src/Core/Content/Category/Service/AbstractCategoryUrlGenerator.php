<?php declare(strict_types=1);

namespace Laser\Core\Content\Category\Service;

use Laser\Core\Content\Category\CategoryEntity;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelEntity;

#[Package('content')]
abstract class AbstractCategoryUrlGenerator
{
    abstract public function getDecorated(): AbstractCategoryUrlGenerator;

    abstract public function generate(CategoryEntity $category, ?SalesChannelEntity $salesChannel): ?string;
}
