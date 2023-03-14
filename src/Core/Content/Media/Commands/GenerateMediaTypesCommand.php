<?php declare(strict_types=1);

namespace Laser\Core\Content\Media\Commands;

use Laser\Core\Content\Media\File\MediaFile;
use Laser\Core\Content\Media\MediaEntity;
use Laser\Core\Content\Media\TypeDetector\TypeDetector;
use Laser\Core\Framework\Adapter\Console\LaserStyle;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\Log\Package;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'media:generate-media-types',
    description: 'Generates media types for all media files',
)]
#[Package('content')]
class GenerateMediaTypesCommand extends Command
{
    private LaserStyle $io;

    private ?int $batchSize = null;

    /**
     * @internal
     */
    public function __construct(
        private readonly TypeDetector $typeDetector,
        private readonly EntityRepository $mediaRepository
    ) {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->addOption('batch-size', 'b', InputOption::VALUE_REQUIRED, 'Batch Size')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new LaserStyle($input, $output);

        $context = Context::createDefaultContext();
        $this->batchSize = $this->validateBatchSize($input);

        $this->io->comment('Starting to generate MediaTypes. This may take some time...');
        $this->io->progressStart($this->getMediaCount($context));

        $this->detectMediaTypes($context);

        $this->io->progressFinish();

        return self::SUCCESS;
    }

    private function validateBatchSize(InputInterface $input): int
    {
        $batchSize = $input->getOption('batch-size');
        if ($batchSize === null) {
            return 100;
        }

        if (!is_numeric($batchSize)) {
            throw new \RuntimeException('BatchSize is not numeric');
        }

        return (int) $batchSize;
    }

    private function getMediaCount(Context $context): int
    {
        $criteria = new Criteria();
        $criteria->setTotalCountMode(Criteria::TOTAL_COUNT_MODE_EXACT);
        $criteria->setLimit(1);
        $result = $this->mediaRepository->search($criteria, $context);

        return $result->getTotal();
    }

    private function detectMediaTypes(Context $context): void
    {
        $criteria = $this->createCriteria();

        do {
            $result = $this->mediaRepository->search($criteria, $context);
            foreach ($result->getEntities() as $media) {
                $this->detectMediaType($context, $media);
            }
            $this->io->progressAdvance($result->count());
            $criteria->setOffset($criteria->getOffset() + $this->batchSize);
        } while ($result->getTotal() > $this->batchSize);
    }

    private function detectMediaType(Context $context, MediaEntity $media): void
    {
        if (!$media->hasFile()) {
            return;
        }

        $file = new MediaFile(
            $media->getUrl(),
            $media->getMimeType(),
            $media->getFileExtension(),
            $media->getFileSize()
        );

        $type = $this->typeDetector->detect($file);
        $changeSet = ['id' => $media->getId(), 'mediaTypeRaw' => serialize($type)];

        $context->scope(Context::SYSTEM_SCOPE, function (Context $context) use ($changeSet): void {
            $this->mediaRepository->upsert([$changeSet], $context);
        });
    }

    private function createCriteria(): Criteria
    {
        $criteria = new Criteria();
        $criteria->setTotalCountMode(Criteria::TOTAL_COUNT_MODE_NEXT_PAGES);
        $criteria->setLimit($this->batchSize);

        return $criteria;
    }
}
