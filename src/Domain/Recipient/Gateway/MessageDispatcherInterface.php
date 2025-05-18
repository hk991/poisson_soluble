<?php

declare(strict_types=1);

namespace Domain\Recipient\Gateway;

use Domain\Recipient\Entity\Recipient;

interface MessageDispatcherInterface
{
    public function dispatch(Recipient $recipient, string $message): void;
}
