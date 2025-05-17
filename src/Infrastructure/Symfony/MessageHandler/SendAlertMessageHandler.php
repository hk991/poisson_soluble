<?php

declare(strict_types=1);

namespace Infrastructure\Symfony\MessageHandler;

use Domain\Event\Recipient\SendAlertMessage;
use Domain\Gateway\AlertSenderServiceInterface;
use Domain\Gateway\RecipientRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SendAlertMessageHandler implements MessageHandlerInterface
{
    private RecipientRepositoryInterface $recipientRepository;
    private AlertSenderServiceInterface $alertSender;

    public function __construct(
        RecipientRepositoryInterface $recipientRepository,
        AlertSenderServiceInterface $alertSender,
    ) {
        $this->recipientRepository = $recipientRepository;
        $this->alertSender = $alertSender;
    }

    public function __invoke(SendAlertMessage $message)
    {
        $recipients = $this->recipientRepository->findByInsee($message->getInsee());

        if (empty($recipients)) {
            return;
        }

        foreach ($recipients as $recipient) {
            $this->alertSender->send($recipient, $message->getMessage());
        }
    }
}
