<?php

declare(strict_types=1);

namespace Infrastructure\Symfony\Service;

use Domain\Recipient\Entity\Recipient;
use Domain\Recipient\Event\SendAlertMessage;
use Domain\Recipient\Gateway\MessageDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class MessageDispatcher implements MessageDispatcherInterface
{
    private MessageBusInterface $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    public function dispatch(Recipient $recipient, string $message): void
    {
        $alertMessage = new SendAlertMessage($recipient->getPhone(), $message);

        $this->bus->dispatch($alertMessage);
    }
}
