<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Recipient\UseCase;

use Domain\Recipient\Entity\Recipient;
use Domain\Recipient\Gateway\LoggingGatewayInterface;
use Domain\Recipient\Gateway\RecipientRepositoryInterface;
use Domain\Recipient\Request\ImportRecipientsFromCsvRequest;
use Domain\Recipient\Response\ImportRecipientsFromCsvResponse;
use Domain\Recipient\UseCase\ImportRecipientsFromCsvUseCase;
use PHPUnit\Framework\TestCase;

class ImportRecipientsFromCsvUseCaseTest extends TestCase
{
    private RecipientRepositoryInterface $recipientRepository;
    private LoggingGatewayInterface $logger;
    private ImportRecipientsFromCsvUseCase $useCase;

    protected function setUp(): void
    {
        $this->recipientRepository = $this->createMock(RecipientRepositoryInterface::class);
        $this->logger = $this->createMock(LoggingGatewayInterface::class);
        $this->useCase = new ImportRecipientsFromCsvUseCase(
            $this->recipientRepository,
            $this->logger
        );
    }

    public function testExecuteWithRealCsvFile(): void
    {
        $filePath = __DIR__.'/../../../../../public/recipients.csv';

        $this->assertFileExists($filePath, "Le fichier CSV de test est introuvable à : $filePath");

        $this->recipientRepository
            ->expects($this->atLeastOnce())
            ->method('insert')
            ->with($this->isInstanceOf(Recipient::class));

        $this->logger
            ->expects($this->once())
            ->method('logInfo');

        $request = new ImportRecipientsFromCsvRequest($filePath);

        $response = $this->useCase->execute($request);

        $this->assertInstanceOf(ImportRecipientsFromCsvResponse::class, $response);
        $this->assertTrue($response->isSuccess());
    }
}
