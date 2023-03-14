<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Customer\Command;

use Laser\Core\Checkout\Customer\DeleteUnusedGuestCustomerService;
use Laser\Core\Framework\Adapter\Console\LaserStyle;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Log\Package;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'customer:delete-unused-guests',
    description: 'Delete unused guest customers',
)]
#[Package('customer-order')]
class DeleteUnusedGuestCustomersCommand extends Command
{
    /**
     * @internal
     */
    public function __construct(private readonly DeleteUnusedGuestCustomerService $deleteUnusedGuestCustomerService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new LaserStyle($input, $output);

        $context = Context::createDefaultContext();

        $count = $this->deleteUnusedGuestCustomerService->countUnusedCustomers($context);

        if ($count === 0) {
            $io->comment('No unused guest customers found.');

            return self::SUCCESS;
        }

        $confirm = $io->confirm(
            \sprintf('Are you sure that you want to delete %d guest customers?', $count),
            false
        );

        if (!$confirm) {
            $io->caution('Aborting due to user input.');

            return self::SUCCESS;
        }

        $progressBar = $io->createProgressBar($count);

        do {
            $ids = $this->deleteUnusedGuestCustomerService->deleteUnusedCustomers($context);
            $progressBar->advance(\count($ids));
        } while ($ids);

        $progressBar->finish();

        $io->success(\sprintf('Successfully deleted %d guest customers.', $count));

        return self::SUCCESS;
    }
}
