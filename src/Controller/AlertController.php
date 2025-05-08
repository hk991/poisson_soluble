<?php

declare(strict_types=1);

namespace App\Controller;

use App\Message\SendSMSMessage;
use App\Repository\RecipientRepository;
use App\Security\ApiKeyAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class AlertController extends AbstractController
{
    #[Route('/alerter', name: 'send_alerts', methods: ['POST'])]
    public function sendAlerts(
        Request $request,
        RecipientRepository $repository,
        MessageBusInterface $bus,
        ApiKeyAuthenticator $authenticator,
    ): JsonResponse {
        if ($request->isMethod('POST') && $request->headers->get('Content-Type') !== 'application/json') {
            return new JsonResponse(['error' => 'Incorrect Content-Type. Expected application/json.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $authenticator->validate($request);

        $data = json_decode($request->getContent(), true);
        if (!\is_array($data) || !isset($data['insee'])) {
            return new JsonResponse(['error' => 'Missing or invalid JSON payload'], 400);
        }

        $recipients = $repository->findByInsee($data['insee']);

        foreach ($recipients as $recipient) {
            $bus->dispatch(new SendSMSMessage(
                $recipient['phone'],
                'test message.'
            ));
        }

        return new JsonResponse(['message' => 'SMS queued for delivery.'], JsonResponse::HTTP_OK);
    }
}
