<?php declare(strict_types=1);

namespace Laser\Core\Content\Media\Commands;

use Laser\Core\Content\Media\DeleteNotUsedMediaService;
use Laser\Core\Framework\Adapter\Console\LaserStyle;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\ArrayStruct;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'media:delete-unused',
    description: 'Deletes all media files which are not used in any entity',
)]
#[Package('content')]
class DeleteNotUsedMediaCommand extends Command
{
    /**
     * @internal
     */
    public function __construct(private readonly DeleteNotUsedMediaService $deleteMediaService)
    {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->addOption('folder-entity', null, InputOption::VALUE_REQUIRED, 'Restrict deletion of not used media in default location folders of the provided entity name');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new LaserStyle($input, $output);

        $context = Context::createDefaultContext();

        if (\is_string($input->getOption('folder-entity'))) {
            $context->addExtension(
                DeleteNotUsedMediaService::RESTRICT_DEFAULT_FOLDER_ENTITIES_EXTENSION,
                new ArrayStruct([strtolower($input->getOption('folder-entity'))])
            );
        }

        $count = $this->deleteMediaService->countNotUsedMedia($context);

        if ($count === 0) {
            $io->comment('No unused media files found.');

            return self::SUCCESS;
        }

        $confirm = $io->confirm(sprintf('Are you sure that you want to delete %d media files?', $count), false);

        if (!$confirm) {
            $io->caution('Aborting due to user input.');

            return self::SUCCESS;
        }

        $this->deleteMediaService->deleteNotUsedMedia($context);
        $io->success(sprintf('Successfully deleted %d media files.', $count));

        return self::SUCCESS;
    }
}
