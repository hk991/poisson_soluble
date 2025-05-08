<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\SendSMSMessage;
use App\Service\SmsService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendSMSMessageHandler
{
    public function __construct(
        private SmsService $smsService,
    ) {
    }

    public function __invoke(SendSMSMessage $message): void
    {
        $this->smsService->send(
            $message->getPhone(),
            $message->getMessage()
        );
    }
}
