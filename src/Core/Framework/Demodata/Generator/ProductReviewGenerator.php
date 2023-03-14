<?php declare(strict_types=1);

namespace Laser\Core\Framework\Demodata\Generator;

use Doctrine\DBAL\Connection;
use Laser\Core\Content\Product\Aggregate\ProductReview\ProductReviewDefinition;
use Laser\Core\Defaults;
use Laser\Core\Framework\DataAbstractionLayer\Write\EntityWriterInterface;
use Laser\Core\Framework\DataAbstractionLayer\Write\WriteContext;
use Laser\Core\Framework\Demodata\DemodataContext;
use Laser\Core\Framework\Demodata\DemodataGeneratorInterface;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Uuid\Uuid;

/**
 * @internal
 */
#[Package('core')]
class ProductReviewGenerator implements DemodataGeneratorInterface
{
    /**
     * @internal
     */
    public function __construct(
        private readonly EntityWriterInterface $writer,
        private readonly ProductReviewDefinition $productReviewDefinition,
        private readonly Connection $connection
    ) {
    }

    public function getDefinition(): string
    {
        return ProductReviewDefinition::class;
    }

    /**
     * @param array<mixed> $options
     */
    public function generate(int $numberOfItems, DemodataContext $context, array $options = []): void
    {
        $context->getConsole()->progressStart($numberOfItems);

        $customerIds = $this->getCustomerIds();
        $productIds = $this->getProductIds();
        $salesChannelIds = $this->connection->fetchFirstColumn('SELECT LOWER(HEX(id)) FROM sales_channel');
        $points = [1, 2, 3, 4, 5];

        $payload = [];

        for ($i = 0; $i < $numberOfItems; ++$i) {
            $payload[] = [
                'id' => Uuid::randomHex(),
                'productId' => $context->getFaker()->randomElement($productIds),
                'customerId' => $context->getFaker()->randomElement($customerIds),
                'salesChannelId' => $salesChannelIds[array_rand($salesChannelIds)],
                'languageId' => Defaults::LANGUAGE_SYSTEM,
                'externalUser' => $context->getFaker()->name,
                'externalEmail' => $context->getFaker()->email,
                'title' => $context->getFaker()->sentence(),
                'content' => $context->getFaker()->text(),
                'points' => $context->getFaker()->randomElement($points),
                'status' => (bool) random_int(0, 1),
            ];
        }

        $writeContext = WriteContext::createFromContext($context->getContext());

        foreach (array_chunk($payload, 100) as $chunk) {
            $this->writer->upsert($this->productReviewDefinition, $chunk, $writeContext);
            $context->getConsole()->progressAdvance(\count($chunk));
        }

        $context->getConsole()->progressFinish();
    }

    /**
     * @return array<string>
     */
    private function getCustomerIds(): array
    {
        $sql = 'SELECT LOWER(HEX(id)) as id FROM customer LIMIT 200';

        $customerIds = $this->connection->fetchAllAssociative($sql);

        return array_column($customerIds, 'id');
    }

    /**
     * @return array<string>
     */
    private function getProductIds(): array
    {
        $sql = 'SELECT LOWER(HEX(id)) as id FROM product WHERE version_id = :liveVersionId LIMIT 200';

        $productIds = $this->connection->fetchAllAssociative($sql, ['liveVersionId' => Uuid::fromHexToBytes(Defaults::LIVE_VERSION)]);

        return array_column($productIds, 'id');
    }
}
