<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Recipient;
use App\Repository\RecipientRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'import-recipients',
    description: 'Import recipients from a CSV file',
)]
final class ImportRecipientsFromCsvCommand extends Command
{
    public function __construct(
        private readonly RecipientRepository $recipientRepository,
    ) {
        parent::__construct();
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

        if (!file_exists($filePath)) {
            $io->error('The file does not exist');

            return Command::FAILURE;
        }

        if (($handle = fopen($filePath, 'r')) === false) {
            $io->error('Cannot open the file');

            return Command::FAILURE;
        }

        $rowsImportedCount = 0;
        $rowsFailedCount = 0;
        $errors = [];

        $headers = fgetcsv($handle);
        if (!$headers || !\in_array('insee', $headers) || !\in_array('phone', $headers)) {
            $io->error("The CSV file must contain 'insee' and 'phone' fields");
            fclose($handle);

            return Command::FAILURE;
        }

        while (($data = fgetcsv($handle)) !== false) {
            $insee = $data[array_search('insee', $headers)];
            $phone = $data[array_search('phone', $headers)];

            $recipient = new Recipient($insee, $phone);

            if ($recipient->validateInsee() && $recipient->validatePhone()) {
                $this->recipientRepository->insert($recipient);
                ++$rowsImportedCount;
            } else {
                ++$rowsFailedCount;
                $errors[] = "Invalid row: INSEE={$insee}, phone={$phone}";
            }
        }

        fclose($handle);

        $successMessage = \sprintf('Import completed, %1$d rows imported successfully, %2$d rows failed', $rowsImportedCount, $rowsFailedCount);

        $io->success($successMessage);

        if ($rowsFailedCount > 0) {
            $io->text('Errors:');
            foreach ($errors as $error) {
                $io->error($error);
            }
        }

        return Command::SUCCESS;
    }
}
