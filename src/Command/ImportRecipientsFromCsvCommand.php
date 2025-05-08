<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Recipient;
use App\Repository\RecipientRepository;
use Psr\Log\LoggerInterface;
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
        private readonly LoggerInterface $businessLogger,
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
        $startTime = microtime(true);
        $io = new SymfonyStyle($input, $output);
        $filePath = $input->getArgument('file');

        if (!file_exists($filePath)) {
            $io->error('The file does not exist');
            $this->businessLogger->error("Import failed: file not found at path '$filePath'");

            return Command::FAILURE;
        }

        if (($handle = fopen($filePath, 'r')) === false) {
            $io->error('Cannot open the file');
            $this->businessLogger->error("Import failed: cannot open file '$filePath'");

            return Command::FAILURE;
        }

        $rowsImportedCount = 0;
        $rowsFailedCount = 0;
        $errors = [];

        $headers = fgetcsv($handle);
        if (!$headers || !\in_array('insee', $headers) || !\in_array('phone', $headers)) {
            $io->error("The CSV file must contain 'insee' and 'phone' fields");
            fclose($handle);
            $this->businessLogger->error("Import failed: invalid headers in file '$filePath'");

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

        $duration = microtime(true) - $startTime;
        $successMessage = \sprintf(
            'Import completed: %d rows imported, %d rows failed (Duration: %.2f seconds)',
            $rowsImportedCount,
            $rowsFailedCount,
            $duration
        );

        $this->businessLogger->info($successMessage);
        foreach ($errors as $error) {
            $this->businessLogger->warning($error);
        }

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
