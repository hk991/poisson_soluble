<?php

declare(strict_types=1);

namespace Domain\Recipient\UseCase;

use Domain\Recipient\Entity\Recipient;
use Domain\Recipient\Gateway\LoggingGatewayInterface;
use Domain\Recipient\Gateway\RecipientRepositoryInterface;
use Domain\Recipient\Request\ImportRecipientsFromCsvRequest;
use Domain\Recipient\Response\ImportRecipientsFromCsvResponse;

class ImportRecipientsFromCsvUseCase
{
    public function __construct(
        private readonly RecipientRepositoryInterface $recipientRepository,
        private readonly LoggingGatewayInterface $logger,
    ) {
    }

    public function execute(ImportRecipientsFromCsvRequest $request): ImportRecipientsFromCsvResponse
    {
        $startTime = microtime(true);
        $rowsImportedCount = 0;
        $rowsFailedCount = 0;
        $errors = [];

        $filePath = $request->filePath;

        if (!file_exists($filePath)) {
            $this->logger->logError("Import failed: file not found at path '$filePath'");

            return new ImportRecipientsFromCsvResponse(false, 'The file does not exist', []);
        }

        if (($handle = fopen($filePath, 'r')) === false) {
            $this->logger->logError("Import failed: cannot open file '$filePath'");

            return new ImportRecipientsFromCsvResponse(false, 'Cannot open the file', []);
        }

        $headers = fgetcsv($handle);
        if (!$headers || !\in_array('insee', $headers) || !\in_array('phone', $headers)) {
            $this->logger->logError("Import failed: invalid headers in file '$filePath'");
            fclose($handle);

            return new ImportRecipientsFromCsvResponse(false, "The CSV file must contain 'insee' and 'phone' fields", []);
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

        $this->logger->logInfo($successMessage);
        foreach ($errors as $error) {
            $this->logger->logError($error);
        }

        return new ImportRecipientsFromCsvResponse(true, $successMessage, $errors);
    }
}
