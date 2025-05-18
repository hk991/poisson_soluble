<?php

declare(strict_types=1);

namespace Domain\Recipient\Gateway;

use Domain\Recipient\Entity\Recipient;

interface AlertSenderServiceInterface
{
    public function send(Recipient $recipient, string $message): void;
}
