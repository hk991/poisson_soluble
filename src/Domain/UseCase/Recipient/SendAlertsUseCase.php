<?php

declare(strict_types=1);

namespace Domain\UseCase\Recipient;

use Domain\Gateway\ApiKeyValidatorInterface;
use Domain\Gateway\MessageDispatcherInterface;
use Domain\Gateway\RecipientRepositoryInterface;
use Domain\Request\Recipient\SendAlertsRequest;
use Domain\Response\Recipient\SendAlertsResponse;

class SendAlertsUseCase
{
    public function __construct(
        private readonly RecipientRepositoryInterface $recipientRepository,
        private readonly MessageDispatcherInterface $messageDispatcher,
        private readonly ApiKeyValidatorInterface $apiKeyValidator,
    ) {
    }

    public function sendAlerts(SendAlertsRequest $request): SendAlertsResponse
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
