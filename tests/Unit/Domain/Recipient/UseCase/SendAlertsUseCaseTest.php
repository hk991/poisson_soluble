<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Recipient\UseCase;

use Domain\Recipient\Entity\Recipient;
use Domain\Recipient\Gateway\ApiKeyValidatorInterface;
use Domain\Recipient\Gateway\MessageDispatcherInterface;
use Domain\Recipient\Gateway\RecipientRepositoryInterface;
use Domain\Recipient\Request\SendAlertsRequest;
use Domain\Recipient\Response\SendAlertsResponse;
use Domain\Recipient\UseCase\SendAlertsUseCase;
use PHPUnit\Framework\TestCase;

class SendAlertsUseCaseTest extends TestCase
{
    private RecipientRepositoryInterface $recipientRepository;
    private MessageDispatcherInterface $messageDispatcher;
    private ApiKeyValidatorInterface $apiKeyValidator;
    private SendAlertsUseCase $useCase;

    protected function setUp(): void
    {
        $this->recipientRepository = $this->createMock(RecipientRepositoryInterface::class);
        $this->messageDispatcher = $this->createMock(MessageDispatcherInterface::class);
        $this->apiKeyValidator = $this->createMock(ApiKeyValidatorInterface::class);

        $this->useCase = new SendAlertsUseCase(
            $this->recipientRepository,
            $this->messageDispatcher,
            $this->apiKeyValidator
        );
    }

    public function test_it_sends_alerts_successfully(): void
    {
        $request = $this->createMock(SendAlertsRequest::class);
        $request->method('getInsee')->willReturn('77777');

        $this->apiKeyValidator
            ->expects($this->once())
            ->method('validate')
            ->with($request);

        $recipient = new Recipient('77777', '0601020304');

        $this->recipientRepository
            ->expects($this->once())
            ->method('findByInsee')
            ->with('77777')
            ->willReturn([$recipient]);

        $this->messageDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($recipient, 'test message.');

        $response = $this->useCase->execute($request);

        $this->assertInstanceOf(SendAlertsResponse::class, $response);
        $this->assertTrue($response->isSuccess());
        $this->assertSame('SMS queued for delivery.', $response->getMessage());
    }

    public function test_it_returns_unauthorized_when_api_key_is_invalid(): void
    {
        $request = $this->createMock(SendAlertsRequest::class);

        $this->apiKeyValidator
            ->expects($this->once())
            ->method('validate')
            ->with($request)
            ->willThrowException(new \Exception('Invalid API Key'));

        $response = $this->useCase->execute($request);

        $this->assertInstanceOf(SendAlertsResponse::class, $response);
        $this->assertFalse($response->isSuccess());
        $this->assertSame('Unauthorized: Invalid API Key', $response->getMessage());
    }

    public function test_it_returns_error_when_no_recipients_found(): void
    {
        $request = $this->createMock(SendAlertsRequest::class);
        $request->method('getInsee')->willReturn('000000');

        $this->apiKeyValidator
            ->expects($this->once())
            ->method('validate')
            ->with($request);

        $this->recipientRepository
            ->expects($this->once())
            ->method('findByInsee')
            ->with('000000')
            ->willReturn([]);

        $response = $this->useCase->execute($request);

        $this->assertInstanceOf(SendAlertsResponse::class, $response);
        $this->assertFalse($response->isSuccess());
        $this->assertSame('No recipients found for the provided INSEE.', $response->getMessage());
    }
}
