<?php

declare(strict_types=1);

namespace Infrastructure\Symfony\MessageHandler;

use Domain\Recipient\Event\SendAlertMessage;
use Domain\Recipient\Gateway\AlertSenderServiceInterface;
use Domain\Recipient\Gateway\RecipientRepositoryInterface;
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
