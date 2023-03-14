<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\SearchKeyword;

use Laser\Core\Content\Product\ProductEntity;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Log\Package;

#[Package('inventory')]
interface ProductSearchKeywordAnalyzerInterface
{
    /**
     * @param array<int, array{field: string, tokenize: bool, ranking: int}> $configFields
     */
    public function analyze(ProductEntity $product, Context $context, array $configFields): AnalyzedKeywordCollection;
}
