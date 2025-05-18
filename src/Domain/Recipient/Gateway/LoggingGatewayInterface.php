<?php

declare(strict_types=1);

namespace Domain\Recipient\Gateway;

interface LoggingGatewayInterface
{
    public function logInfo(string $message, array $context = []): void;

    public function logError(string $message, array $context = []): void;
}
