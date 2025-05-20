<?php

declare(strict_types=1);

namespace Domain\Recipient\UseCase;

use Domain\Recipient\Gateway\ApiKeyValidatorInterface;
use Domain\Recipient\Gateway\MessageDispatcherInterface;
use Domain\Recipient\Gateway\RecipientRepositoryInterface;
use Domain\Recipient\Request\SendAlertsRequest;
use Domain\Recipient\Response\SendAlertsResponse;

class SendAlertsUseCase
{
    public function __construct(
        private readonly RecipientRepositoryInterface $recipientRepository,
        private readonly MessageDispatcherInterface $messageDispatcher,
        private readonly ApiKeyValidatorInterface $apiKeyValidator,
    ) {
    }

    public function execute(SendAlertsRequest $request): SendAlertsResponse
    {
        try {
            $this->apiKeyValidator->validate($request);
        } catch (\Exception $e) {
            return new SendAlertsResponse(false, 'Unauthorized: '.$e->getMessage());
        }

        $insee = $request->getInsee();
        $recipients = $this->recipientRepository->findByInsee($insee);

        if (empty($recipients)) {
            return new SendAlertsResponse(false, 'No recipients found for the provided INSEE.');
        }

        foreach ($recipients as $recipient) {
            $this->messageDispatcher->dispatch($recipient, 'test message.');
        }

        return new SendAlertsResponse(true, 'SMS queued for delivery.');
    }
}
