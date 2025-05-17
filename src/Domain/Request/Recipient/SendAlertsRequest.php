<?php

declare(strict_types=1);

namespace Domain\Request\Recipient;

class SendAlertsRequest
{
    private string $insee;
    private ?string $apiKey = null;

    public function __construct(string $insee, string $apiKey)
    {
        $this->insee = $insee;
        $this->apiKey = $apiKey;
    }

    public function getInsee(): string
    {
        return $this->insee;
    }

    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }
}
