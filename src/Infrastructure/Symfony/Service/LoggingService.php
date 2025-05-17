<?php

declare(strict_types=1);

namespace Infrastructure\Symfony\Service;

use Domain\Gateway\LoggingGatewayInterface;
use Psr\Log\LoggerInterface;

class LoggingService implements LoggingGatewayInterface
{
    public function __construct(
        private readonly LoggerInterface $businessLogger,
    ) {
    }

    public function logInfo(string $message, array $context = []): void
    {
        $this->businessLogger->info($message, $context);
    }

    public function logError(string $message, array $context = []): void
    {
        $this->businessLogger->error($message, $context);
    }
}
