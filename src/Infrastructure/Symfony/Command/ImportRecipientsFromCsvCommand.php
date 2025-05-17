<?php

declare(strict_types=1);

namespace Infrastructure\Symfony\Command;

use Domain\Request\Recipient\ImportRecipientsFromCsvRequest;
use Domain\UseCase\Recipient\ImportRecipientsFromCsvUseCase;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'import-recipients',
    description: 'Import recipients from a CSV file'
)]
final class ImportRecipientsFromCsvCommand extends Command
{
    private ImportRecipientsFromCsvUseCase $importRecipientsUseCase;

    public function __construct(ImportRecipientsFromCsvUseCase $importRecipientsUseCase)
    {
        parent::__construct();
        $this->importRecipientsUseCase = $importRecipientsUseCase;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Import a CSV file containing recipient information (INSEE, phone)')
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the CSV file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filePath = $input->getArgument('file');

        $request = new ImportRecipientsFromCsvRequest($filePath);

        $response = $this->importRecipientsUseCase->execute($request);

        if ($response->isSuccess()) {
            $io->success($response->getMessage());
        } else {
            $io->error($response->getMessage());
        }

        if (!empty($response->errors)) {
            $io->text('Errors:');
            foreach ($response->getErrors() as $error) {
                $io->error($error);
            }
        }

        return $response->isSuccess() ? Command::SUCCESS : Command::FAILURE;
    }
}
