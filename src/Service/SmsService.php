<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;

final class SmsService
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    public function send(string $phone, string $message): void
    {
        $logMessage = \sprintf(
            'SMS envoyé à %s : "%s" [%s]',
            $phone,
            $message,
            (new \DateTime())->format('Y-m-d H:i:s')
        );

        $this->logger->info($logMessage);
    }
}
