<?php

declare(strict_types=1);

namespace Infrastructure\Symfony\Service;

use Domain\Entity\Recipient;
use Domain\Event\Recipient\SendAlertMessage;
use Domain\Gateway\MessageDispatcherInterface;
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
