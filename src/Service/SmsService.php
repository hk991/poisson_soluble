<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;

final class SmsService
{
    public function __construct(
        private readonly LoggerInterface $businessLogger,
    ) {
    }

    public function send(string $phone, string $message): void
    {
        $logMessage = \sprintf(
            'SMS sent to %s : "%s" [%s]',
            $phone,
            $message,
            (new \DateTime())->format('Y-m-d H:i:s')
        );

        $this->businessLogger->info($logMessage);
    }
}
