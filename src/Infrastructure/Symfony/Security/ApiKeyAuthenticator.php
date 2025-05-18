<?php

declare(strict_types=1);

namespace Infrastructure\Symfony\Security;

use Domain\Recipient\Gateway\ApiKeyValidatorInterface;
use Domain\Recipient\Request\SendAlertsRequest;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ApiKeyAuthenticator implements ApiKeyValidatorInterface
{
    public function __construct(
        private readonly string $apiKey,
    ) {
    }

    public function validate(SendAlertsRequest $sendAlertsRequest): void
    {
        if ($this->apiKey !== $sendAlertsRequest->getApiKey()) {
            throw new UnauthorizedHttpException('', 'Invalid or missing API key');
        }
    }
}
