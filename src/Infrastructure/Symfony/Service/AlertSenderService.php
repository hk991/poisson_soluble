<?php

declare(strict_types=1);

namespace Infrastructure\Symfony\Service;

use Domain\Recipient\Entity\Recipient;
use Domain\Recipient\Gateway\AlertSenderServiceInterface;
use Domain\Recipient\Gateway\LoggingGatewayInterface;

class AlertSenderService implements AlertSenderServiceInterface
{
    public function __construct(
        private readonly LoggingGatewayInterface $logging,
    ) {
    }

    public function send(Recipient $recipient, string $message): void
    {
        $logMessage = \sprintf(
            'SMS sent to %s : "%s" [%s]',
            $recipient->getPhone(),
            $message,
            (new \DateTime())->format('Y-m-d H:i:s')
        );

        $this->logging->logInfo($logMessage);
    }
}
