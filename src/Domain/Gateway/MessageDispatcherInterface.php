<?php

declare(strict_types=1);

namespace Domain\Gateway;

use Domain\Entity\Recipient;

interface MessageDispatcherInterface
{
    public function dispatch(Recipient $recipient, string $message): void;
}
